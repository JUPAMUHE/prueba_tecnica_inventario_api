<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'in:admin,user',
        ], [
            'name.required' => 'El nombre de usuario es requerido.',
            'email.unique' => 'El correo electrónico ya está registrado en nuestro sistema.',
            'email.required' => 'El correo es requerido.',
            'password.required' => 'La constraseña es requerida.',
            'password.min' => 'La constraseña debe ser minimo de 6 caracteres',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // Por defecto todos son user
        $role = 'user';

        // Si está autenticado y es admin, puede fijar el rol
        if (auth()->check() && auth()->user()->role === 'admin' && isset($validated['role'])) {
            $role = $validated['role'];
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $role,
        ]);

        return response()->json(['message' => 'Usuario registrado', 'data' => $user], 201);
    }

    public function login(Request $request)
    {
        // Validación de las credenciales
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'El correo es requerido.',
            'password.required' => 'La constraseña es requerida.',
            'password.min' => 'La constraseña debe ser de minimo 6 caracteres.',
        ]);

        // Intentar autenticar al usuario
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $user = Auth::user();
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'data' => $user,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Credenciales incorrectas'], 401);
    }

    public function logout(Request $request)
    {
        // Revoca el token actual del usuario autenticado
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

}
