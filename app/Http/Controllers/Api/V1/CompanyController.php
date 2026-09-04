<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::paginate($request->get('per_page', 15));
        return response()->json($companies);
    }

    public function show(Company $company)
    {
        return response()->json($company);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $company->update($validated);

        return response()->json($company);
    }
}