<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Item;
use App\Models\Submodule;
use App\Models\Permission_item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['submodule:id,submodule', 'aurora_app:id,app'])->get();

        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $items, 
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
        $item = Item::with(['submodule:id,submodule', 'aurora_app:id,app'])->find($id);

        if (!$item) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $item, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'app_id' => 'required',
                'submodule_id' => 'required',
                'item' => [
                    'required',
                    Rule::unique('items')->where(function ($query) use ($request) {
                        return $query->where('app_id', $request->app_id);
                    }),
                ],
                'path' => 'required',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('item');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $submodule = Submodule::find($request->submodule_id);
        if (!is_object($submodule)) {
            return response()->json([
                'message' => 'No se encontró el Submódulo', 
                'status' => 404
            ], 404);
        }

        $item = Item::create([
            'app_id' => $request->app_id,
            'submodule_id' => $request->submodule_id,
            'item' => $request->item,
            'path' => $request->path,
            'description' => $request->description,
            'icon' => $request->icon == '' ? '<i class="fa-solid fa-circle"></i>' : $request->icon,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Item registrado exitosamente', 
            'data' => $item, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'app_id' => 'required',
                'submodule_id' => 'required',
                'item' => [
                    'required',
                    Rule::unique('items', 'item')->ignore($id, 'id')->where(function ($query) use ($request) {
                        return $query->where('app_id', $request->app_id);
                    }),
                ],
                'path' => 'required',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('item');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $item->app_id = $request->app_id;
        $item->item = $request->item;
        $item->submodule_id = $request->submodule_id;
        $item->path = $request->path;
        $item->description = $request->description;
        $item->icon = $request->icon == '' ? '<i class="fa-solid fa-circle"></i>' : $request->icon;
        $item->status = $request->status;
        $item->save();

        return response()->json([
            'message' => 'Item editado exitosamente', 
            'data' => $item, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        $permissions = Permission_item::where('item', $id)
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
                'message' => 'El item no se puede eliminar porque tiene permisos asignados',
                'status' => 400
            ], 400);
        }

        Permission_item::where('item', $id)->delete();
        $item->delete();

        return response()->json([
            'message' => 'Item eliminado exitosamente', 
            'data' => $item, 
            'status' => 200
        ], 200);
    }
    
}
