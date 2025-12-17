<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $subdomain  El subdominio esperado (ej: 'admin')
     */
    public function handle(Request $request, Closure $next, string $subdomain): Response
    {
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // Obtener el subdominio (primera parte del host)
        // Ejemplo: admin.rebornrentals.com -> 'admin'
        // Ejemplo: www.rebornrentals.com -> 'www'
        // Ejemplo: rebornrentals.com -> null
        $currentSubdomain = count($parts) > 2 ? $parts[0] : null;
        
        // En desarrollo local con localhost, permitir acceso (las rutas se registran con prefijo /admin)
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1']) 
            || str_contains($host, 'localhost')
            || str_starts_with($host, '127.0.0.1');
        $isLocalAdmin = app()->environment('local') && $isLocalhost;
        
        // Si no es el subdominio correcto y no es localhost, abortar con 404
        if ($currentSubdomain !== $subdomain && !$isLocalAdmin) {
            abort(404, 'Subdomain not found');
        }
        
        return $next($request);
    }
}
