<?php

namespace App\Http\Controllers\Erp;

use App\Models\Erp\EntregaFest\EntregaFestHistorialComunicacion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProspectoEntregaFest;
use App\Models\CopropietarioEntregaFest;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\CorreoContacto;
use App\Models\CorreoCampana;
use Illuminate\Support\Facades\DB;

class ProspectoEntregaFestController extends Controller
{
    public function marcarEnviado(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo;
        $canal = $request->canal;
        $mensaje = $request->mensaje;

        $persona = ($tipo === 'Propietario')
            ? ProspectoEntregaFest::find($id)
            : CopropietarioEntregaFest::find($id);

        if (!$persona)
            return response()->json(['error' => 'No encontrado'], 404);

        if ($canal === 'whatsapp') {
            // --- WHATSAPP LOGIC ---
            $wa_contacto = WhatsappContacto::firstOrCreate(
                ['numero_celular' => $persona->celular],
                ['wa_id' => $persona->celular, 'nombre_wa' => $persona->nombres]
            );

            $conversacion = WhatsappConversacion::firstOrCreate(
                ['contacto_id' => $wa_contacto->id],
                ['estado' => 'cerrado']
            );

            WhatsappMensaje::create([
                'conversacion_id' => $conversacion->id,
                'direccion' => 'saliente',
                'tipo' => 'texto',
                'contenido' => $mensaje ?? 'Invitación enviada por WhatsApp',
                'wa_message_id' => 'mass_' . uniqid(),
                'estado' => 'enviado'
            ]);

        } elseif ($canal === 'email') {
            // --- EMAIL LOGIC ---

            // 1. Aseguramos la Lista
            $lista_id = DB::table('correo_listas')->insertGetId([
                'nombre' => 'EntregaFest',
                'created_at' => now(),
                'updated_at' => now()
            ]) ?? DB::table('correo_listas')->where('nombre', 'EntregaFest')->value('id');

            // 2. Aseguramos la Plantilla (Corregido: 'asunto' y 'cuerpo')
            $plantilla_id = DB::table('correo_plantillas')->value('id');
            if (!$plantilla_id) {
                $plantilla_id = DB::table('correo_plantillas')->insertGetId([
                    'nombre' => 'Plantilla Base',
                    'asunto' => 'Invitación EntregaFest',
                    'cuerpo' => 'Invitación EntregaFest',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 3. Contacto
            $correo_contacto = CorreoContacto::firstOrCreate(
                ['email' => $persona->email],
                ['nombres' => $persona->nombres]
            );

            // 4. Campaña
            $campana = CorreoCampana::firstOrCreate(
                ['nombre' => 'Pre-Invitación EntregaFest'],
                [
                    'asunto' => 'Invitación EntregaFest',
                    'lista_id' => $lista_id,
                    'plantilla_id' => $plantilla_id,
                    'estado' => 'COMPLETADO'
                ]
            );

            // 5. Envío
            DB::table('correo_campana_envios')->insert([
                'campana_id' => $campana->id,
                'contacto_id' => $correo_contacto->id,
                'estado' => 'ENVIADO',
                'enviado_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- HISTORIAL ESPECIALIZADO ENTREGA FEST ---
        // Aquí registramos específicamente la interacción del sistema de eventos
        EntregaFestHistorialComunicacion::create([
            'persona_id' => $persona->id,
            'persona_type' => get_class($persona), // Detecta si es App\Models\ProspectoEntregaFest o Copropietario
            'canal' => $canal, // whatsapp o email
            'etapa' => 'pre-invitacion',
            'estado' => 'enviado',
            'fecha_envio' => now()
        ]);

        return response()->json(['message' => 'Status e Historial registrados correctamente']);
    }
}
