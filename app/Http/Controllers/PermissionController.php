<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Module;

class PermissionController extends Controller
{
    public function showByRole($id) 
    {
        $reqModule = Module::where('status', '!=', 0)->select('id', 'module')->get();
        $reqPermissions = Permission::where('rol', $id)->get();
    
        $reqPermissionsDefault = [
            'r' => 0,
            'w' => 0,
            'u' => 0,
            'd' => 0
        ];
    
        $reqPermissionsRol = [
            'role' => $id
        ];
        
        if (empty($reqPermissions)) {
            for ($i=0; $i < count($reqModule); $i++) { 
                $reqModule[$i]['permissions'] = $reqPermissionsDefault;
            }
        } else {
            for ($i=0; $i < count($reqModule); $i++) { 
                $reqPermissionsDefault = array(
                    'r' => 0,
                    'w' => 0,
                    'u' => 0,
                    'd' => 0
                );
                if (isset($reqPermissions[$i])) {
                    $reqPermissionsDefault = array(
                        'r' => $reqPermissions[$i]['r'],
                        'w' => $reqPermissions[$i]['w'],
                        'u' => $reqPermissions[$i]['u'],
                        'd' => $reqPermissions[$i]['d'] 
                    );
                }
                $reqModule[$i]['permissions'] = $reqPermissionsDefault;  
            }   
        }

        $reqPermissionsRol['modules'] = $reqModule;
    
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $reqPermissionsRol, 
            'status' => 200
        ], 200);
    }

    public function showByModule($mId, $rId) 
    {
        $reqPermissions = Permission::modulesPermissions($rId);

        $permissions = ['message' => 'There are no permissions assigned for this module.'];
                    
        if (count($reqPermissions) > 0) {
            $permissions = isset($reqPermissions[$mId]) ? $reqPermissions[$mId] : "";
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $permissions, 
            'status' => 200
        ], 200);
    }

    public function showModulesByRole($id) 
    {
        $permissions = Permission::modulesPermissions($id);

        $modulePermissions = ['message' => 'No hay permisos asignados para este módulo.'];

        if (count($permissions) > 0) {
            $modulePermissions = $permissions;
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $modulePermissions, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'rol' => 'required',
                'module' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $errors ? 'All fields are required' : null;
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        Permission::where('rol', $request->rol)->delete();

        foreach ($request->module as $module) {
            $id = $module['id'];
            $r = empty($module['r']) ? 0 : 1;
            $w = empty($module['w']) ? 0 : 1;
            $u = empty($module['u']) ? 0 : 1;
            $d = empty($module['d']) ? 0 : 1;

            $permissions = Permission::create([
                'rol' => $request->rol,
                'module' => $id,
                'r' => $r,
                'w' => $w,
                'u' => $u,
                'd' => $d,
            ]);
        }

        return response()->json([
            'message' => 'Permisos asignados exitosamente', 
            'data' => $permissions, 
            'status' => 201
        ], 201);
    }
}
