<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
        then: function () {
            $host = request()->getHost();
            $parts = explode('.', $host);
            $subdomain = count($parts) > 2 ? $parts[0] : null;
            
            // Detectar localhost (con o sin puerto)
            $isLocalhost = in_array($host, ['localhost', '127.0.0.1']) 
                || str_contains($host, 'localhost')
                || str_starts_with($host, '127.0.0.1');
            
            // En producción: funciona con subdominio admin.rebornrentals.com O con prefijo /admin
            if (!app()->environment('local')) {
                if ($subdomain === 'admin') {
                    // Si hay subdominio admin, usar subdominio (sin prefijo)
                    Route::middleware(['web', 'subdomain:admin'])
                        ->group(base_path('routes/admin.php'));
                } else {
                    // Si no hay subdominio, usar prefijo /admin (para Hostinger y otros hosts)
                    Route::middleware(['web'])
                        ->prefix('admin')
                        ->group(base_path('routes/admin.php'));
                }
            }
            
            // En desarrollo local: funciona con prefijo /admin en localhost
            // O si hay subdominio admin en local también
            if (app()->environment('local')) {
                if ($subdomain === 'admin') {
                    // Si hay subdominio admin en local, usar subdominio (sin prefijo)
                    Route::middleware(['web', 'subdomain:admin'])
                        ->group(base_path('routes/admin.php'));
                } else {
                    // Si es localhost sin subdominio, usar prefijo /admin
                    Route::middleware(['web'])
                        ->prefix('admin')
                        ->group(base_path('routes/admin.php'));
                }
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'subdomain' => \App\Http\Middleware\SubdomainMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
