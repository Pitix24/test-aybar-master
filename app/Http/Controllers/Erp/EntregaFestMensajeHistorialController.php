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

class EntregaFestMensajeHistorialController extends Controller
{
    public function mensajeHistorial(Request $request)
    {
        $id = $request->id;
        $tipo = $request->tipo;
        $canal = $request->canal;
        $mensaje = $request->mensaje;

        $persona = ($tipo === 'Propietario')
            ? ProspectoEntregaFest::find($id)
            : CopropietarioEntregaFest::with('prospecto')->find($id);

        if (!$persona)
            return response()->json(['error' => 'No encontrado'], 404);

        // Preparamos nombres dinámicos para los reportes
        $etapaNombre = ucwords(str_replace(['-', '_'], ' ', $request->etapa ?? 'pre-invitacion'));
        $eventoNombre = $persona->entregaFest->nombre ?? 'EntregaFest';

        $estadoReal = strtolower($request->estado ?? 'enviado');

        // Mapeo para cada tabla según sus Enums
        $estadoEmail = ($estadoReal === 'enviado') ? 'ENVIADO' : 'ERROR';
        $estadoWhatsapp = ($estadoReal === 'enviado') ? 'enviado' : 'fallido';
        $estadoHistorial = ($estadoReal === 'enviado') ? 'enviado' : 'fallido';

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
                'contenido' => $mensaje ?? "Envío de {$etapaNombre} para {$eventoNombre}",
                'wa_message_id' => 'mass_' . uniqid(),
                'estado' => $estadoWhatsapp
            ]);

        } elseif ($canal === 'email') {
            // --- EMAIL LOGIC ---

            // 1. Aseguramos la Lista
            $lista_id = DB::table('correo_listas')->insertGetId([
                'nombre' => "Lista - {$eventoNombre}",
                'created_at' => now(),
                'updated_at' => now()
            ]) ?? DB::table('correo_listas')->where('nombre', "Lista - {$eventoNombre}")->value('id');

            // 2. Aseguramos la Plantilla
            $plantilla_id = DB::table('correo_plantillas')->value('id');
            if (!$plantilla_id) {
                $plantilla_id = DB::table('correo_plantillas')->insertGetId([
                    'nombre' => "Plantilla - {$etapaNombre}",
                    'asunto' => "Invitación {$eventoNombre}",
                    'cuerpo' => "Invitación {$eventoNombre}",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 3. Contacto
            $correo_contacto = CorreoContacto::firstOrCreate(
                ['email' => $persona->email],
                ['nombres' => $persona->nombres]
            );

            // 4. Campaña Dinámica
            $campana = CorreoCampana::firstOrCreate(
                ['nombre' => "{$etapaNombre} - {$eventoNombre}"],
                [
                    'asunto' => "Invitación {$eventoNombre}",
                    'lista_id' => $lista_id,
                    'plantilla_id' => $plantilla_id,
                    'estado' => 'COMPLETADO'
                ]
            );

            // 5. Envío
            DB::table('correo_campana_envios')->insert([
                'campana_id' => $campana->id,
                'contacto_id' => $correo_contacto->id,
                'estado' => $estadoEmail,
                'error_mensaje' => ($estadoEmail === 'ERROR') ? ($request->mensaje_error ?? 'Fallo en envío SMTP') : null,
                'enviado_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // --- HISTORIAL ESPECIALIZADO ENTREGA FEST ---
        // Aquí registramos específicamente la interacción del sistema de eventos
        EntregaFestHistorialComunicacion::create([
            'entrega_fest_id' => $persona->entrega_fest_id ?? ($persona->prospecto->entrega_fest_id ?? 0),
            'persona_id' => $persona->id,
            'persona_type' => get_class($persona), // Detecta si es App\Models\ProspectoEntregaFest o Copropietario
            'canal' => $canal, // whatsapp o email
            'etapa' => $request->etapa ?? 'pre-invitacion',
            'estado' => $estadoHistorial,
            'fecha_envio' => now(),
            'metadata' => $request->all() // Se guarda como JSON gracias al cast 'array' en el modelo
        ]);

        return response()->json(['message' => 'Status e Historial registrados correctamente']);
    }
}
