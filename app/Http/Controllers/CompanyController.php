<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User_company;

class CompanyController extends Controller
{
    public function index()
    {
        $companys = Company::all();
        
        return response()->json([
            'message' => 'Registros encontrados', 
            'data' => $companys, 
            'status' => 200
        ], 200);
    }

    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        return response()->json([
            'message' => 'Registro encontrado', 
            'data' => $company, 
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:companies,name',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('name');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $company = Company::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Empresa registrada exitosamente', 
            'data' => $company, 
            'status' => 201
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:companies,name,' . $id . ',id',
                'status' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $error = $errors->get('name');
            $message = $error ? 'This record already exists.' : 'All fields are required';
            
            return response()->json([
                'message' => $message, 
                'status' => 422
            ], 422);
        }

        $company->name = $request->name;
        $company->description = $request->description;
        $company->status = $request->status;
        $company->save();

        return response()->json([
            'message' => 'Empresa editada exitosamente', 
            'data' => $company, 
            'status' => 200
        ], 200);
    }

    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'message' => 'No se encontró el registro', 
                'status' => 404
            ], 404);
        }

        $user_companies = User_company::where('company', $id)->get();
        foreach ($user_companies as $user_company) {
            if ($user_company->status == 1) {
                return response()->json([
                    'message' => 'No se puede eliminar una empresa que esté asociada a un usuario.'
                ], 422);
            }
        }

        $company->delete();

        return response()->json([
            'message' => 'Empresa eliminada exitosamente', 
            'data' => $company, 
            'status' => 200
        ], 200);
    }
}
