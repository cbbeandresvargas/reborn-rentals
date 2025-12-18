<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            $subdomain = count($parts) > 2 ? $parts[0] : null;
            $isLocalhost = in_array($host, ['localhost', '127.0.0.1']) || str_contains($host, 'localhost');
            
            // Si estamos en el subdominio admin o en ruta /admin (local o producción)
            if ($subdomain === 'admin' || $request->is('admin*')) {
                return redirect()->route('admin.login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
            }
            
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        if (Auth::user()->role !== 'admin') {
            $host = $request->getHost();
            $parts = explode('.', $host);
            $subdomain = count($parts) > 2 ? $parts[0] : null;
            
            // Si no es admin y está en el subdominio admin o ruta /admin (local o producción)
            if ($subdomain === 'admin' || $request->is('admin*')) {
                return redirect()->route('home')->with('error', 'No tienes permisos para acceder al panel de administración.');
            }
            
            return redirect()->route('home')->with('error', 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}

