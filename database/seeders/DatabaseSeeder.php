<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            RegionSeeder::class,
            ProvinciaSeeder::class,
            DistritoSeeder::class,
            RolesYPermisosSeeder::class,
            MenuSeeder::class,

            /*UserSeeder::class,
            UnidadNegocioSeeder::class,
            SedesYAreasSeeder::class,
            GrupoProyectoSeeder::class,
            ProyectoSeeder::class,
            TipoSolicitudSeeder::class,
            EstadoTicketSeeder::class,
            PrioridadTicketSeeder::class,
            CanalSeeder::class,
            TicketSeeder::class,
            TicketParticipanteSeeder::class,
            TicketMensajeSeeder::class,
            TicketArchivoSeeder::class,
            TicketHistorialSeeder::class,
            TicketDerivadoSeeder::class,
            TicketEmailSeeder::class,*/
        ]);
    }
}
