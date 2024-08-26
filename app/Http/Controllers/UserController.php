<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\User_role;
use App\Models\User_company;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        foreach ($users as $user) {
            $userRoles = User::userRoles($user);
            $userCompanies = User::userCompanies($user);
            $user->roles = $userRoles;
            $user->companies = $userCompanies;
        }
        
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $users, 
            'status' => 200
        ], 200);
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

        $user->roles = User::userRoles($user);
        $user->companies = User::userCompanies($user); 

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $user, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'lastname' => 'required',
                'username' => 'required|unique:users,username',
                'rol_id' => 'required',
                'company_id' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'status' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $errors;
            if ($errors->get('username')) {
                $message = 'Please change the username.';
            }
            if ($errors->get('email')) {
                $message = 'Please change the email.';
            }
            if ($errors->get('password')) {
                $message = 'The password must have at least 6 characters.';
            }
            if ($errors->get('name') || $errors->get('lastname') || $errors->get('rol_id') 
                || $errors->get('company_id') || $errors->get('status')) {
                $message = 'All fields are required';
            }

            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status
        ]);

        $roles = Role::where('status', 1)->get();
        foreach ($roles as $rol) {
            $id = $rol['id'];
            $status = $rol['id'] == $request->rol_id ? 1 : 0; 
            
            User_role::create([
                'user' => $user->id,
                'rol' => $id,
                'status' => $status
            ]);
        }

        $userRoles = User_role::select('roles.rol')->join('roles', 'user_roles.rol', '=', 'roles.id')
        ->where('user_roles.user', $user->id)->where('user_roles.status', 1)->get();

        $user->roles = $userRoles;

        $companies = Company::where('status', 1)->get();
        foreach ($companies as $company) {
            $id = $company['id'];
            $status = $company['id'] == $request->company_id ? 1 : 0; 
            
            User_company::create([
                'user' => $user->id,
                'company' => $id,
                'status' => $status
            ]);
        }

        $userCompanies = User_company::select('companies.name')->join('companies', 'user_companies.company', '=', 'companies.id')
        ->where('user_companies.user', $user->id)->where('user_companies.status', 1)->get();

        $user->companies = $userCompanies;

        return response()->json([
            'message' => 'Usuario registrado exitosamente', 
            'data' => $user, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'lastname' => 'required',
                'username' => 'required|unique:users,username,' . $id . ',id',
                'email' => 'required|email|unique:users,email,' . $id . ',id',
                'status' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            if ($errors->get('username')) {
                $message = 'This record already exists, please change the username.';
            }
            if ($errors->get('email')) {
                $message = 'This record already exists, please change the email.';
            }
            if ($errors->get('password')) {
                $message = 'The password must have at least 6 characters.';
            }
            if ($errors->get('name') || $errors->get('lastname') || $errors->get('rol_id') 
                || $errors->get('company_id') || $errors->get('status')) {
                $message = 'All fields are required';
            }
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->username = $request->username;
        $user->email = $request->email;
        if (isset($request->password) && $request->password != '' && $request->password != null) {
            $user->password = Hash::make($request->password);
        }
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'message' => 'Usuario editado exitosamente', 
            'data' => $user, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }
        
        User_role::where('user', $id)->delete();
        User_company::where('user', $id)->delete();
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente', 
            'data' => $user, 
            'status' => 200
        ], 200);
    }
}
