<?php

use Illuminate\Support\Facades\Schedule;

// Eliminar extractos bancarios expirados (diariamente a las 2 AM)
Schedule::command('contaplus:delete-expired-statements')->dailyAt('02:00');

// Procesar documentos pendientes (cada hora)
Schedule::command('contaplus:process-documents')->hourly();

// Verificar límites de documentos (diariamente a las 9 AM)
Schedule::command('contaplus:check-limits')->dailyAt('09:00');
