<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Submodule;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::all();
        return response()->json(['items' => $items], 200);
    }

    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json(['item' => $item], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'submodule_id' => 'required',
                'item' => 'required|unique:items,item',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('item');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $submodule = Submodule::find($request->submodule_id);
        if (!is_object($submodule)) {
            return response()->json(['message' => 'Submodule not found'], 404);
        }

        $item = Item::create([
            'submodule_id' => $request->submodule_id,
            'item' => $request->item,
            'path' => $request->path,
            'description' => $request->description,
            'icon' => $request->icon,
            'status' => $request->status,
        ]);

        return response()->json(
            ['message' => 'Item registered successfully', 'item' => $item], 
            201
        );
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        try {
            $validatedData = $request->validate([
                'item' => 'required|unique:items,item,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('item');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $item->item = $request->item;
        $item->path = $request->path;
        $item->description = $request->description;
        $item->icon = $request->icon;
        $item->status = $request->status;
        $item->save();

        return response()->json(['message' => 'Item updated successfully', 'item' => $item], 200);
    }

    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully', 'item' => $item], 200);
    }
}
