<?php

use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Services\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        // Ckeck if the application is in maintenance mode and apply the MaintenanceModeMiddleware to API routes
        $middleware->api(prepend: [
            MaintenanceModeMiddleware::class
        ]);

        // Rate limiting for API routes
        $middleware->api(append: [
            ThrottleRequests::class . ':api', // use the 'api' rate limiter defined in the application's configuration
        ]);

        // Sanctum ability middleware alias
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );

        // Custom exception for rate limit exceeded
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            return ApiResponse::error('Too many requests. Please try again later.', 429);
        });

        // Sanctum AccessDeniedHttpException
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: $e->getMessage(),
                    code: 403,
                );
            }
        });

        // Capture validation exceptions and return a structured JSON response
        // If a ValidationException is thrown, this will catch it and return a JSON response with the validation errors.
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    code: 422,
                    errors: $e->errors()
                );
            }
        });

        // exception for everything else
        $exceptions->render(function (\Exception $e, Request $request) {
            if ($request->is('api/*')) {

                // Solve the sanctum login missing route
                if ($e->getMessage() === 'Route [login] not defined.') {
                    return ApiResponse::error(
                        message: 'Invalid or missing authentication token. Please log in again.',
                        code: 401,

                    );
                }
                return ApiResponse::error(
                    message: 'An unexpected error occurred. Please try again later.',
                    code: 500,
                    errors: [$e->getMessage()]
                );
            };
        });

    })->create();
