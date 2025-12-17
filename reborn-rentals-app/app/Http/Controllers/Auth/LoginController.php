<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirigir según el rol del usuario y el subdominio
            if (Auth::user()->isAdmin()) {
                $host = $request->getHost();
                $parts = explode('.', $host);
                $subdomain = count($parts) > 2 ? $parts[0] : null;
                $isLocalhost = in_array($host, ['localhost', '127.0.0.1']) || str_contains($host, 'localhost');
                
                // Si estamos en el subdominio admin, redirigir al dashboard
                if ($subdomain === 'admin') {
                    return redirect()->intended('/');
                }
                
                // Si estamos en localhost (desarrollo), redirigir a /admin
                if (app()->environment('local') && $isLocalhost) {
                    return redirect()->intended('/admin');
                }
                
                // En producción, redirigir al subdominio admin
                $adminUrl = 'http://admin.' . config('app.domain', 'rebornrentals.com');
                return redirect($adminUrl);
            }

            // Si no es admin y está intentando acceder al subdominio admin, redirigir al dominio principal
            $host = $request->getHost();
            $parts = explode('.', $host);
            $subdomain = count($parts) > 2 ? $parts[0] : null;
            
            if ($subdomain === 'admin') {
                $mainUrl = 'http://' . config('app.domain', 'rebornrentals.com');
                return redirect($mainUrl)->with('error', 'No tienes permisos para acceder al panel de administración.');
            }

            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

