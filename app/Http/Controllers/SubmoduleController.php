<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submodule;
use App\Models\Module;

class SubmoduleController extends Controller
{
    public function index()
    {
        $submodules = Submodule::all();

        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $submodules, 
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
        $submodule = Submodule::find($id);

        if (!$submodule) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $submodule, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'module_id' => 'required',
                'submodule' => 'required|unique:submodules,submodule',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('submodule');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $module = Module::find($request->module_id);
        if (!is_object($module)) {
            return response()->json([
                'message' => 'No se encontró el módulo', 
                'status' => 404
            ], 404);
        }

        $submodule = Submodule::create([
            'module_id' => $request->module_id,
            'submodule' => $request->submodule,
            'path' => $request->path,
            'description' => $request->description,
            'icon' => $request->icon,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Submódulo registrado exitosamente', 
            'data' => $submodule, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $submodule = Submodule::find($id);

        if (!$submodule) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'submodule' => 'required|unique:submodules,submodule,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('submodule');
            $message = $error ? 'This record already exists.' : 'All fields are required';
           
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $submodule->submodule = $request->submodule;
        $submodule->path = $request->path;
        $submodule->description = $request->description;
        $submodule->icon = $request->icon;
        $submodule->status = $request->status;
        $submodule->save();

        return response()->json([
            'message' => 'Submódulo editado exitosamente', 
            'data' => $submodule, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $submodule = Submodule::find($id);

        if (!$submodule) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        if ($submodule->items()->count() > 0) {
            return response()->json([
                'message' => 'El submódulo no se puede eliminar porque tiene items asociados',
                'status' => 400
            ], 400);
        }

        $submodule->delete();

        return response()->json([
            'message' => 'Submódulo eliminado exitosamente', 
            'data' => $submodule, 
            'status' => 200
        ], 200);
    }
}
