<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['expenseType', 'createdBy', 'bankAccount'])
            ->latest()
            ->paginate(10);
        $expenseTypes = ExpenseType::all();
        $bankAccounts = BankAccount::where('is_active', 1)->get();
        return view('frontend.pages.expenses.index', compact('expenses', 'expenseTypes', 'bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:payment_mode,bank|exists:bank_accounts,id|nullable',
            'date_time' => 'required|date',
            'payment_mode' => 'required|in:cash,credit,bank',
            'payment_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        $expense = Expense::create($validated + ['created_by' => auth()->id()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully',
                'expense' => $expense->load('expenseType', 'createdBy', 'bankAccount')
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return response()->json([
            'expense' => $expense->load('expenseType', 'bankAccount')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:payment_mode,bank|exists:bank_accounts,id|nullable',
            'date_time' => 'required|date',
            'payment_mode' => 'required|in:cash,credit,bank',
            'payment_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'expense' => $expense->load('expenseType', 'createdBy', 'bankAccount')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        $page = request()->input('page', 1);
        $expenses = Expense::with(['expenseType', 'createdBy', 'bankAccount'])
            ->latest()
            ->paginate(10, ['*'], 'page', $page);

        if ($expenses->isEmpty() && $page > 1) {
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted successfully',
                'redirect' => true,
                'redirectUrl' => route('expenses.index', ['page' => $page - 1])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
            'expenses' => $expenses->items()
        ]);
    }
}
