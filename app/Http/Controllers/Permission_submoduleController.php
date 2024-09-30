<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission_submodule;
use App\Models\Submodule;
use App\Models\Role;

class Permission_submoduleController extends Controller
{
    public function showByRole($id) 
    {
        $appRole = Role::where('id', $id)->with(['aurora_app:id,app'])->select('id', 'app_id')->first();
        $reqSubmodule = Submodule::where('status', '!=', 0)->where('app_id', $appRole->app_id)->with('module:id,module')
        ->select('id', 'app_id', 'submodule', 'module_id')->get();
        $reqPermissions = Permission_submodule::where('rol', $id)->get();
    
        $reqPermissionsDefault = [
            'r' => 0,
            'w' => 0,
            'u' => 0,
            'd' => 0
        ];
    
        $reqPermissionsRol = [
            'role' => $id,
            'app' => $appRole->aurora_app->app
        ];
        
        if (empty($reqPermissions)) {
            for ($i=0; $i < count($reqSubmodule); $i++) { 
                $reqSubmodule[$i]['permissions'] = $reqPermissionsDefault;
            }
        } else {
            for ($i=0; $i < count($reqSubmodule); $i++) { 
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
                $reqSubmodule[$i]['permissions'] = $reqPermissionsDefault;  
            }   
        }

        $reqPermissionsRol['submodules'] = $reqSubmodule;
    
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $reqPermissionsRol, 
            'status' => 200
        ], 200);
    }

    public function showBySubmodule($smId, $rId) 
    {
        $reqPermissions = Permission_submodule::submodulesPermissions($rId);

        $permissions = ['message' => 'No hay permisos asignados para este submódulo.'];
                    
        if (count($reqPermissions) > 0) {
            $permissions = isset($reqPermissions[$smId]) ? $reqPermissions[$smId] : "";
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $permissions, 
            'status' => 200
        ], 200);
    }

    public function showSubmodulesByRole($id) 
    {
        $permissions = Permission_submodule::submodulesPermissions($id);

        $submodulePermissions = ['message' => 'No hay permisos asignados para este submódulo.'];

        if (count($permissions) > 0) {
            $submodulePermissions = $permissions;
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $submodulePermissions, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'rol' => 'required',
                'submodule' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $errors ? 'All fields are required' : null;
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        Permission_submodule::where('rol', $request->rol)->delete();

        foreach ($request->submodule as $submodule) {
            $id = $submodule['id'];
            $r = empty($submodule['r']) ? 0 : 1;
            $w = empty($submodule['w']) ? 0 : 1;
            $u = empty($submodule['u']) ? 0 : 1;
            $d = empty($submodule['d']) ? 0 : 1;

            $permissions = Permission_submodule::create([
                'rol' => $request->rol,
                'submodule' => $id,
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
