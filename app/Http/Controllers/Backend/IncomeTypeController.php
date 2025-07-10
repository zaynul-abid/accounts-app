<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\IncomeType;
use Illuminate\Http\Request;

class IncomeTypeController extends Controller
{
    public function index()
    {
        // Get income types only for the current user's company
        $incomeTypes = IncomeType::where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.pages.income-types.index', compact('incomeTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:income_types,name',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'type' => 'required|in:Direct Income,Indirect Income',
        ]);

        // Add company_id to the validated data
        $data = array_merge($validated, ['company_id' => auth()->user()->company_id]);

        $incomeType = IncomeType::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Income Type created successfully',
            'incomeType' => [
                'id' => $incomeType->id,
                'name' => $incomeType->name,
                'description' => $incomeType->description,
                'status' => $incomeType->status,
                'type' => $incomeType->type,
                'company_id' => $incomeType->company_id // Include company_id in response
            ]
        ]);
    }

    public function update(Request $request, IncomeType $incomeType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:income_types,name,' . $incomeType->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'type' => 'required|in:Direct Income,Indirect Income',
        ]);

        $incomeType->update($validated);

        return response()->json(['message' => 'Income Type updated successfully']);
    }

    public function destroy(IncomeType $incomeType)
    {
        if ($incomeType->status) {
            return response()->json([
                'message' => 'Cannot delete active income type'
            ], 422);
        }

        $incomeType->delete();

        return response()->json(['message' => 'Income Type deleted successfully']);
    }
}


