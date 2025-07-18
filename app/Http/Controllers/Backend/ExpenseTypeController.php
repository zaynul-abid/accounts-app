<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        try {
            $expenseTypes = ExpenseType::where('company_id', auth()->user()->company_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('backend.pages.expense-types.index', compact('expenseTypes'));
        } catch (\Exception $e) {
            Log::error('Expense Type Index Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while loading expense types.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:expense_types,name,NULL,id,company_id,' . auth()->user()->company_id,
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'type' => 'required|in:Direct Expense,Indirect Expense',
            ]);

            $data = array_merge($validated, ['company_id' => auth()->user()->company_id]);

            $expenseType = ExpenseType::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Expense Type created successfully',
                'expenseType' => [
                    'id' => $expenseType->id,
                    'name' => $expenseType->name,
                    'description' => $expenseType->description,
                    'status' => $expenseType->status,
                    'type' => $expenseType->type,
                    'company_id' => $expenseType->company_id
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Expense Type Store Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the expense type'
            ], 500);
        }
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        try {
            // Ensure the expense type belongs to the user's company
            if ($expenseType->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this expense type'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id . ',id,company_id,' . auth()->user()->company_id,
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'type' => 'required|in:Direct Expense,Indirect Expense',
            ]);

            $expenseType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Expense Type updated successfully',
                'expenseType' => [
                    'id' => $expenseType->id,
                    'name' => $expenseType->name,
                    'description' => $expenseType->description,
                    'status' => $expenseType->status,
                    'type' => $expenseType->type,
                    'company_id' => $expenseType->company_id
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Expense Type Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the expense type'
            ], 500);
        }
    }

    public function destroy(ExpenseType $expenseType)
    {
        try {
            // Ensure the expense type belongs to the user's company
            if ($expenseType->company_id !== auth()->user()->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this expense type'
                ], 403);
            }

            if ($expenseType->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active expense type'
                ], 422);
            }

            $expenseType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense Type deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Expense Type Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the expense type'
            ], 500);
        }
    }
}
