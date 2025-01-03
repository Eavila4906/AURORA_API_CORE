<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aurora_app;
use App\Models\Role;
use App\Models\Module;
use App\Models\Submodule;
use App\Models\Item;

class Aurora_appController extends Controller
{
    public function index()
    {
        $apps = Aurora_app::all();
        
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $apps, 
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
        $app = Aurora_app::find($id);

        if (!$app) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $app, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'app' => 'required|unique:aurora_apps,app',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('app');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $app = Aurora_app::create([
            'app' => $request->app,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'App registrada exitosamente', 
            'data' => $app, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $app = Aurora_app::find($id);

        if (!$app) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'app' => 'required|unique:aurora_apps,app,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('app');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $app->app = $request->app;
        $app->description = $request->description;
        $app->status = $request->status;
        $app->save();

        return response()->json([
            'message' => 'App editada exitosamente', 
            'data' => $app, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $app = Aurora_app::find($id);

        if (!$app) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        $rol = Role::where('app_id', $id)->get();
        if ($rol->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar esta app ya que cuenta con roles asignados.',
                'status' => 422
            ], 422);
        }

        $module = Module::where('app_id', $id)->get();
        if ($module->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar esta app ya que cuenta con módulos asignados.',
                'status' => 422
            ], 422);
        }

        $submodule = Submodule::where('app_id', $id)->get();
        if ($submodule->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar esta app ya que cuenta con submódulos asignados.',
                'status' => 422
            ], 422);
        }

        $item = Item::where('app_id', $id)->get();
        if ($item->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar esta app ya que cuenta con items asignados.',
                'status' => 422
            ], 422);
        }
        
        $app->delete();

        return response()->json([
            'message' => 'App eliminada exitosamente', 
            'data' => $app, 
            'status' => 200
        ], 200);
    }
}
