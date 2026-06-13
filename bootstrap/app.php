<?php

use App\Http\Middleware\CorrelationIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function (): void {
            //  (ALTERNATIVE TO VERSIONING WITHIN THE URL)
            // routes for version 1 of the API
            // Route::middleware('api')
            // ->prefix('api/v1')
            // ->group(base_path('routes/api_v1.php'));

            //  routes for version 2 of the API
            // Route::middleware('api')
            //     ->prefix('api/v2')
            //     ->group(base_path('routes/api_v2.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
          $middleware->api(prepend: [
            CorrelationIdMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );
    })->create();
