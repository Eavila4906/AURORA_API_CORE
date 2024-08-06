<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission_item;
use App\Models\Item;

class Permission_itemController extends Controller
{
    public function showByRole($id) 
    {
        $reqItem = Item::where('status', '!=', 0)->select('id', 'item')->get();
        $reqPermissions = Permission_item::where('rol', $id)->get();
    
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
            for ($i=0; $i < count($reqItem); $i++) { 
                $reqItem[$i]['permissions'] = $reqPermissionsDefault;
            }
        } else {
            for ($i=0; $i < count($reqItem); $i++) { 
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
                $reqItem[$i]['permissions'] = $reqPermissionsDefault;  
            }   
        }

        $reqPermissionsRol['items'] = $reqItem;
    
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $reqPermissionsRol, 
            'status' => 200
        ], 200);
    }

    public function showByItem($itemId, $rId) 
    {
        $reqPermissions = Permission_item::itemPermissions($rId);

        $permissions = ['message' => 'No hay permisos asignados para estos items.'];
                    
        if (count($reqPermissions) > 0) {
            $permissions = isset($reqPermissions[$itemId]) ? $reqPermissions[$itemId] : "";
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $permissions, 
            'status' => 200
        ], 200);
    }

    public function showItemsByRole($id) 
    {
        $permissions = Permission_item::itemPermissions($id);

        $itemPermissions = ['message' => 'No hay permisos asignados para estos items.'];

        if (count($permissions) > 0) {
            $itemPermissions = $permissions;
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $itemPermissions, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'rol' => 'required',
                'item' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $errors ? 'All fields are required' : null;

            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        Permission_item::where('rol', $request->rol)->delete();

        foreach ($request->item as $item) {
            $id = $item['id'];
            $r = empty($item['r']) ? 0 : 1;
            $w = empty($item['w']) ? 0 : 1;
            $u = empty($item['u']) ? 0 : 1;
            $d = empty($item['d']) ? 0 : 1;

            $permissions = Permission_item::create([
                'rol' => $request->rol,
                'item' => $id,
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
