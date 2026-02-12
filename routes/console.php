<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerarEnviosCavaliDiariosJob;
use App\Jobs\TestSchedulerJob;

// 🧪 JOB DE PRUEBA: Se ejecuta cada minuto para verificar que el scheduler funciona
Schedule::job(new TestSchedulerJob)->everyMinute();

// Ejecutar el job de envíos CAVALI diarios al final del día (23:55)
Schedule::job(new GenerarEnviosCavaliDiariosJob)->dailyAt('23:55');
