<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Module;
use App\Models\Permission;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with(['aurora_app:id,app'])->get();
        
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $modules, 
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
        $module = Module::with(['aurora_app:id,app'])->find($id);

        if (!$module) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $module, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'app_id' => 'required',
                'module' => [
                    'required',
                    Rule::unique('modules')->where(function ($query) use ($request) {
                        return $query->where('app_id', $request->app_id);
                    }),
                ],
                'path' => 'required',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('module');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            

            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $module = Module::create([
            'app_id' => $request->app_id,
            'module' => $request->module,
            'path' => $request->path,
            'description' => $request->description,
            'icon' => $request->icon == '' ? '<i class="fa-solid fa-circle"></i>' : $request->icon,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Módulo registrado exitosamente', 
            'data' => $module, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'app_id' => 'required',
                'module' => [
                    'required',
                    Rule::unique('modules', 'module')->ignore($id, 'id')->where(function ($query) use ($request) {
                        return $query->where('app_id', $request->app_id);
                    }),
                ],
                'path' => 'required',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('module');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $module->app_id = $request->app_id;
        $module->module = $request->module;
        $module->path = $request->path;
        $module->description = $request->description;
        $module->icon = $request->icon == '' ? '<i class="fa-solid fa-circle"></i>' : $request->icon;
        $module->status = $request->status;
        $module->save();

        return response()->json([
            'message' => 'Módulo editado exitosamente', 
            'data' => $module, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $module = Module::find($id);

        if (!$module) 
        {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        $permissions = Permission::where('module', $id)
        ->where(function($query) {
            $query->where('r', 1)
                ->orWhere('w', 1)
                ->orWhere('u', 1)
                ->orWhere('d', 1);
        })
        ->get();
        
        if ($permissions->isNotEmpty()) 
        {
            return response()->json([
                'message' => 'El módulo no se puede eliminar porque tiene permisos asignados',
                'status' => 400
            ], 400);
        }

        if ($module->submodules()->count() > 0) 
        {
            return response()->json([
                'message' => 'El módulo no se puede eliminar porque tiene submódulos asociados',
                'status' => 400
            ], 400);
        }

        Permission::where('module', $id)->delete();
        $module->delete();

        return response()->json([
            'message' => 'Módulo eliminado exitosamente', 
            'data' => $module, 
            'status' => 200
        ], 200);
    }
}
