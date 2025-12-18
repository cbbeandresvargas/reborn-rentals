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

            // Redirigir según el rol del usuario
            if (Auth::user()->isAdmin()) {
                // Siempre redirigir al dashboard del admin usando la ruta /admin
                // Funciona tanto en desarrollo como en producción (Hostinger)
                return redirect()->intended(route('admin.dashboard'));
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

