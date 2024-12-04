<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Empresa;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */




    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->empresas()->count() > 1) {
            // Guarda las empresas en la sesión para acceder en la vista
            $request->session()->flash('empresas', $user->empresas);

            // Redirige a la vista de login
            return redirect()->route('login'); // Ajusta la ruta de redirección a tu vista de login
        }

        // Almacena la empresa seleccionada en la sesión
        session(['empresa' => $user->empresas()->first()]);
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function storeEmpresa(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer|exists:empresas,id',
        ]);

        // Obtener la empresa completa usando el ID
        $empresa = Empresa::with('responsabilidad')->find($request->empresa_id);

        // Almacenar el ID de la empresa seleccionada en la sesión
        session(['empresa' => $empresa]); 

        return redirect()->intended(RouteServiceProvider::HOME);
    }


    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
