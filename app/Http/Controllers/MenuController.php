<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Permission_submodule;
use App\Models\Permission_item;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el rol actual del usuario desde la sesión
        $currentRole = $request->role;

        // Obtener todos los módulos con sus submódulos e ítems
        $modules = Module::with(['submodules.items'])
        ->where('status', 1)->get();

        // Obtener permisos del rol actual
        $modulePermissions = Permission::where('rol', $currentRole)->pluck('r', 'module');
        $submodulePermissions = Permission_submodule::where('rol', $currentRole)->pluck('r', 'submodule');
        $itemPermissions = Permission_item::where('rol', $currentRole)->pluck('r', 'item');

        // Formatear los datos en una estructura amigable
        $menu = $modules->map(function($module) use ($modulePermissions, $submodulePermissions, $itemPermissions) {
            if (!$modulePermissions->get($module->id)) {
                // Si no hay permiso de lectura para el módulo, no lo incluimos
                return null;
            }

            $submodules = $module->submodules->map(function($submodule) use ($submodulePermissions, $itemPermissions) {
                if (!$submodulePermissions->get($submodule->id)) {
                    // Si no hay permiso de lectura para el submódulo, no lo incluimos
                    return null;
                }

                $items = $submodule->items->map(function($item) use ($itemPermissions) {
                    if (!$itemPermissions->get($item->id)) {
                        // Si no hay permiso de lectura para el ítem, no lo incluimos
                        return null;
                    }

                    return [
                        'item_id' => $item->id,
                        'item_name' => $item->item,
                        'item_path' => $item->path,
                        'item_icon' => $item->icon
                    ];
                })->filter(); // Eliminar elementos nulos

                return [
                    'submodule_id' => $submodule->id,
                    'submodule_name' => $submodule->submodule,
                    'submodule_path' => $submodule->path,
                    'submodule_icon' => $submodule->icon,
                    'items' => $items
                ];
            })->filter(); // Eliminar elementos nulos

            return [
                'module_id' => $module->id,
                'module_name' => $module->module,
                'module_path' => $module->path,
                'module_icon' => $module->icon,
                'submodules' => $submodules
            ];
        })->filter(); // Eliminar elementos nulos

        return response()->json([
            'message' => 'Registros encontrados',
            'data' => $menu,
            'status' => 200
        ], 200);
    }
}
