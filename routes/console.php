<?php

use Illuminate\Support\Facades\Schedule;

/* Cleanup */
Schedule::daily()
    ->group(function () {
        Schedule::command('auth:clear-resets');
        Schedule::command('activitylog:clean default');
        Schedule::command('sanctum:prune-expired');
    });
