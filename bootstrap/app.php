<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // Register admin security middleware
        $middleware->alias([
            'admin.ip.whitelist' => \App\Http\Middleware\AdminIPWhitelist::class,
            'admin.rate.limit' => \App\Http\Middleware\AdminRateLimit::class,
            'admin.2fa' => \App\Http\Middleware\AdminTwoFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
