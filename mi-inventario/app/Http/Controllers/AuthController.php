<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ── Mostrar login ──────────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    // ── Procesar login ─────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'El correo o la contraseña son incorrectos.']);
    }

    // ── Mostrar registro ───────────────────────────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.register');
    }

    // ── Procesar registro ──────────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|min:3|max:100',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|string|max:20',
            'department'            => 'required|string',
            'password'              => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'password_confirmation' => 'required',
            'terms'                 => 'accepted',
        ], [
            'name.required'      => 'El nombre completo es obligatorio.',
            'name.min'           => 'El nombre debe tener al menos 3 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingresa un correo electrónico válido.',
            'email.unique'       => 'Este correo ya está registrado.',
            'department.required'=> 'Selecciona un departamento.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.regex'     => 'La contraseña debe tener al menos una mayúscula, un número y un carácter especial.',
            'terms.accepted'     => 'Debes aceptar los términos y condiciones.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // ── Logout ─────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }


    // ── Actualizar perfil ──────────────────────────────────────────────────────
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'  => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update(['name' => $request->name, 'email' => $request->email]);
        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    // ── Cambiar contraseña ─────────────────────────────────────────────────────
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Contraseña cambiada correctamente.');
    }

    // ── Mostrar recuperar contraseña ───────────────────────────────────────
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }
}