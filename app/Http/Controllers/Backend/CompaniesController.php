<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.pages.companies.index', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:companies,name',
            'place' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50|unique:companies,tax_id',
            'status' => 'required|in:active,inactive',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'place' => $company->place,
                'phone' => $company->phone,
                'address' => $company->address,
                'description' => $company->description,
                'tax_id' => $company->tax_id,
                'status' => $company->status
            ]
        ]);
    }

    public function update(Request $request, Company $company)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:companies,name,' . $company->id,
                'place' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
                'tax_id' => 'nullable|string|max:50|unique:companies,tax_id,' . $company->id,
                'status' => 'required|in:active,inactive',
            ]);

            $company->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'place' => $company->place,
                    'phone' => $company->phone,
                    'address' => $company->address,
                    'description' => $company->description,
                    'tax_id' => $company->tax_id,
                    'status' => $company->status
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Company Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Company $company)
    {
        if ($company->status === 'active') {
            return response()->json([
                'message' => 'Cannot delete active company'
            ], 422);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }
}
