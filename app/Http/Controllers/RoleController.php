<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User_role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles], 200);
    }

    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json(['role' => $role], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'rol' => 'required|unique:roles,rol',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('rol');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $role = Role::create([
            'rol' => $request->rol,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Role registered successfully', 'role' => $role], 201);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        try {
            $validatedData = $request->validate([
                'rol' => 'required|unique:roles,rol,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('rol');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $role->rol = $request->rol;
        $role->description = $request->description;
        $role->status = $request->status;
        $role->save();

        return response()->json(['message' => 'Role updated successfully', 'role' => $role], 200);
    }

    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $user_roles = User_role::where('rol', $id)->get();
        foreach ($user_roles as $user_role) {
            if ($user_role->status == 1) {
                return response()->json(['message' => 'You cannot delete a role that is assigned to a user.'], 422);
            }
        }

        $permissions = Permission::where('rol', $id)->get();
        foreach ($permissions as $permission) {
            if ($permission->r == 1 || $permission->w == 1 || $permission->u == 1 || $permission->d == 1) {
                return response()->json(['message' => 'You cannot delete a role that has permissions assigned to it.'], 422);
            }
        }
        
        User_role::where('rol', $id)->delete();
        Permission::where('rol', $id)->delete();
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully', 'role' => $role], 200);
    }
}
