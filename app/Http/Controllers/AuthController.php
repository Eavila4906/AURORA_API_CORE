<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user' => 'required',
                'password' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $errors ? 'All fields are required' : null;

            return response()->json([
                'message' => $message,
                'status' => 422
            ], 422);
        }

        // Verificar si el usuario existe en la base de datos
        $user = User::where('username', $request->user)
                    ->orWhere('email', $request->user)
                    ->first();

        if ($user) {
            // Si el usuario existe pero está inactivo
            if ($user->status != 1) {
                return response()->json([
                    'message' => 'Usuario inactivo, contáctese con el administrador o soporte técnico',
                    'status' => 403
                ], 403);
            }

            // Verificar credenciales
            if (Auth::attempt(['username' => $request->user, 'password' => $request->password]) ||
                Auth::attempt(['email' => $request->user, 'password' => $request->password])) 
            {
                $user = Auth::user();
                $token = $user->createToken($request->email)->accessToken;

                $userRoles = User::userRoles($user);
                $userCompanies = User::userCompanies($user);
                $user->roles = $userRoles;
                $user->companies = $userCompanies;

                $data = [
                    'access_token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'username' => $user->username,
                        'name' => $user->name,
                        'lastname' => $user->lastname,
                        'roles' => $user->roles,
                        'companies' => $user->companies
                    ]
                ];

                return response()->json([
                    'message' => 'Se ha iniciado sesión exitosamente',
                    'data' => $data,
                    'status' => 200
                ], 200);
            }
        }

        // Si el usuario no existe o las credenciales son incorrectas
        return response()->json([
            'message' => 'Credenciales incorrectas',
            'status' => 401
        ], 401);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Se ha cerrado sesión exitosamente',
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => 'Usuario no autenticado',
            'status' => 401
        ], 401);
    }

    public function show() 
    {
        $user = auth()->user();
        $userRoles = User::userRoles($user);
        $userCompanies = User::userCompanies($user);
        $user->roles = $userRoles;
        $user->companies = $userCompanies;
        return response()->json([
            'message' => 'Usuario autenticado',
            'data' => $user,
            'status' => 200
        ], 200);
    }
}