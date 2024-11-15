<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_role;
use App\Models\Role;
use App\Models\User;

class User_roleController extends Controller
{
    public function store(Request $request) 
    {
        try {
            $validatedData = $request->validate([
                'user' => 'required',
                'rol' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $error ? 'All fields are required' : null;
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        User_role::where('user', $request->user)->delete();

        foreach ($request->rol as $rol) {
            $id = $rol['id'];
            $status = empty($rol['status']) ? 0 : 1; 

            $user_roles = User_role::create([
                'user' => $request->user,
                'rol' => $id,
                'status' => $status
            ]);
        }

        return response()->json([
            'message' => 'Roles de usuario asignado exitosamente', 
            'data' => $user_roles, 
            'status' => 201
        ], 201);
    }

    public function show($id) 
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        $roles = Role::where('status', 1)->with(['aurora_app:id,app'])->select('id', 'rol', 'app_id')->get();
        $user_roles = User_role::where('user', $id)->get();

        $UserRoles = array(
            'status' => 0
        );

        $userRoles = array(
            'user' => $id
        );

        if (empty($user_roles)) {
            for ($i=0; $i < count($roles); $i++) { 
                $roles[$i]['userRole'] = $UserRoles;
            }
        } else {
            for ($i=0; $i < count($roles); $i++) { 
                $UserRoles = array(
                    'status' => 0
                );
                if (isset($user_roles[$i])) {
                    $UserRoles = array(
                        'status' => $user_roles[$i]['status']
                    );
                }
                $roles[$i]['userRole'] = $UserRoles;  
            }   
        }
        $userRoles['rol'] = $roles;

        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $userRoles, 
            'status' => 200
        ], 200);
    }
}
