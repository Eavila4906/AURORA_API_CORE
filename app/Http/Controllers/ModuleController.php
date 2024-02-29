<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::all();
        return response()->json(['modules' => $modules], 200);
    }

    public function show($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        return response()->json(['module' => $module], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'module' => 'required|unique:modules,module',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('module');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $module = Module::create([
            'module' => $request->module,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Module registered successfully', 'module' => $module], 201);
    }

    public function update(Request $request, $id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        try {
            $validatedData = $request->validate([
                'module' => 'required|unique:modules,module,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('module');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            return response()->json(['message' => $message], 422);
        }

        $module->module = $request->module;
        $module->description = $request->description;
        $module->status = $request->status;
        $module->save();

        return response()->json(['message' => 'Module updated successfully', 'module' => $module], 200);
    }

    public function destroy($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->delete();

        return response()->json(['message' => 'Module deleted successfully', 'module' => $module], 200);
    }
}
