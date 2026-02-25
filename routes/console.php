<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerarEnviosCavaliDiariosJob;
use App\Jobs\ValidarEnviosCavaliDiariosJob;
use App\Jobs\TestSchedulerJob;

// 🧪 JOB DE PRUEBA: Se ejecuta cada minuto para verificar que el scheduler funciona
Schedule::job(new TestSchedulerJob)->everyMinute();

// Ejecutar el job de envíos CAVALI diarios al final del día (23:55)
Schedule::job(new GenerarEnviosCavaliDiariosJob)->dailyAt('23:55');

// Ejecutar el job de validación de envíos CAVALI (por ejemplo, a las 23:00)
Schedule::job(new ValidarEnviosCavaliDiariosJob)->dailyAt('23:00');

// Ejecutar trabajadores de la cola cada minuto (necesario para Hostinger/Compartidos)
Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();