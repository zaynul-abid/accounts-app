<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $expenseTypes = ExpenseType::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.pages.expense-types.index', compact('expenseTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'type' => 'required|in:Direct Expense,Indirect Expense',
        ]);

       $expenseType = ExpenseType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense Type created successfully',
            'expenseType' => [
                'id' => $expenseType->id,
                'name' => $expenseType->name,
                'description' => $expenseType->description,
                'status' => $expenseType->status,
                'type' => $expenseType->type
             ]
        ]);
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
            'type' => 'required|in:Direct Expense,Indirect Expense',
        ]);

        $expenseType->update($validated);

        return response()->json(['message' => 'Expense Type updated successfully']);
    }

    public function destroy(ExpenseType $expenseType)
    {
        if ($expenseType->status) {
            return response()->json([
                'message' => 'Cannot delete active expense type'
            ], 422);
        }

        $expenseType->delete();

        return response()->json(['message' => 'Expense Type deleted successfully']);
    }
}
