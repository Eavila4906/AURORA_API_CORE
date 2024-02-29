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
            return response()->json(['message' => $message], 422);
        }

        if (Auth::attempt(['username' => $request->user, 'password' => $request->password]) ||
            Auth::attempt(['email' => $request->user, 'password' => $request->password])) 
        {
            $user = Auth::user();
            $token = $user->createToken($request->email)->accessToken;

            $userRoles = User::userRoles($user);
            $user->roles = $userRoles;

            $data = [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                    'roles' => $user->roles
                ]
            ];

            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }

    public function show() 
    {
        $user = auth()->user();
        $userRoles = User::userRoles($user);
        $user->roles = $userRoles;
        return response()->json(['user' => $user]);
    }
}