<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\IncomeType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:income_types,name,NULL,id,company_id,' . auth()->user()->company_id,
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
                    'company_id' => $incomeType->company_id
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Income Type Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the income type'
            ], 500);
        }
    }

    public function update(Request $request, IncomeType $incomeType)
    {
        try {
            // Ensure the income type belongs to the user's company
            if ($incomeType->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this income type'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:income_types,name,' . $incomeType->id . ',id,company_id,' . auth()->user()->company_id,
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'type' => 'required|in:Direct Income,Indirect Income',
            ]);

            $incomeType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Income Type updated successfully',
                'incomeType' => [
                    'id' => $incomeType->id,
                    'name' => $incomeType->name,
                    'description' => $incomeType->description,
                    'status' => $incomeType->status,
                    'type' => $incomeType->type,
                    'company_id' => $incomeType->company_id
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Income Type Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the income type'
            ], 500);
        }
    }

    public function destroy(IncomeType $incomeType)
    {
        try {
            // Ensure the income type belongs to the user's company
            if ($incomeType->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this income type'
                ], 403);
            }

            if ($incomeType->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active income type'
                ], 422);
            }

            $incomeType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Income Type deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Income Type Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the income type'
            ], 500);
        }
    }
}
