<?php

use Illuminate\Support\Facades\Schedule;

// Eliminar extractos bancarios expirados (diariamente a las 2 AM)
Schedule::command('dataflow:delete-expired-statements')->dailyAt('02:00');

// Procesar documentos pendientes (cada hora)
Schedule::command('dataflow:process-documents')->hourly();

// Verificar límites de documentos (diariamente a las 9 AM)
Schedule::command('dataflow:check-limits')->dailyAt('09:00');
