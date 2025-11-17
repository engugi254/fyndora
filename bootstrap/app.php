<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyCsrfToken;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(
            except: [
                'mpesa/callback',  // Your route (case-sensitive, no leading/trailing slash if not needed)
                // Add wildcards if useful, e.g., 'mpesa/*'
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
