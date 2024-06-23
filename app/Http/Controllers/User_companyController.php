<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_company;
use App\Models\User;
use App\Models\Company;

class User_companyController extends Controller
{
    public function store(Request $request) 
    {
        try {
            $validatedData = $request->validate([
                'user' => 'required',
                'company' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            $message = $error ? 'All fields are required' : null;
            return response()->json(['message' => $message], 422);
        }

        User_company::where('user', $request->user)->delete();

        foreach ($request->company as $company) {
            $id = $company['id'];
            $status = empty($company['status']) ? 0 : 1; 

            $user_companies = User_company::create([
                'user' => $request->user,
                'company' => $id,
                'status' => $status
            ]);
        }

        return response()->json([
            'message' => 'User companies assigned successfully', 
            'user_companies' => $user_companies
        ], 201);
    }

    public function show($id) 
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $companies = Company::where('status', 1)->select('id', 'name')->get();
        $user_companies = User_company::where('user', $id)->get();

        $UserCompanies = array(
            'status' => 0
        );

        $userCompanies = array(
            'user' => $id
        );

        if (empty($user_companies)) {
            for ($i=0; $i < count($companies); $i++) { 
                $companies[$i]['userCompany'] = $UserCompanies;
            }
        } else {
            for ($i=0; $i < count($companies); $i++) { 
                $UserCompanies = array(
                    'status' => 0
                );
                if (isset($user_companies[$i])) {
                    $UserCompanies = array(
                        'status' => $user_companies[$i]['status']
                    );
                }
                $companies[$i]['userCompany'] = $UserCompanies;  
            }   
        }
        $userCompanies['name'] = $companies;

        return response()->json(['userCompanies' => $userCompanies], 200);
    }
}
