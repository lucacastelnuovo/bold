<?php

use App\Http\Middleware\RequireJsonMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\LaravelFlare\Facades\Flare;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/api/v1/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            RequireJsonMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Flare::handles($exceptions);
    })
    ->create();
