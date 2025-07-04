<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Income;
use App\Models\IncomeType;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['incomeType', 'createdBy', 'bankAccount'])
            ->latest()
            ->paginate(10);
        $incomeTypes = IncomeType::all();
        $bankAccounts = BankAccount::where('is_active', 1)->get();
        return view('frontend.pages.incomes.index', compact('incomes', 'incomeTypes', 'bankAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_type_id' => 'required|exists:income_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:receipt_mode,bank|exists:bank_accounts,id|nullable',
            'date_time' => 'required|date',
            'receipt_mode' => 'required|in:cash,credit,bank',
            'receipt_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        $income = Income::create($validated + ['created_by' => auth()->id()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Income recorded successfully',
                'income' => $income->load('incomeType', 'createdBy', 'bankAccount')
            ]);
        }

        return redirect()->route('incomes.index')
            ->with('success', 'Income recorded successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Income $income)
    {
        return response()->json([
            'income' => $income->load('incomeType', 'bankAccount')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'income_type_id' => 'required|exists:income_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:receipt_mode,bank|exists:bank_accounts,id|nullable',
            'date_time' => 'required|date',
            'receipt_mode' => 'required|in:cash,credit,bank',
            'receipt_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        $income->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Income updated successfully',
            'income' => $income->load('incomeType', 'createdBy', 'bankAccount')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Income $income)
    {
        $income->delete();

        $page = request()->input('page', 1);
        $incomes = Income::with(['incomeType', 'createdBy', 'bankAccount'])
            ->latest()
            ->paginate(10, ['*'], 'page', $page);

        if ($incomes->isEmpty() && $page > 1) {
            return response()->json([
                'success' => true,
                'message' => 'Income deleted successfully',
                'redirect' => true,
                'redirectUrl' => route('incomes.index', ['page' => $page - 1])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Income deleted successfully',
            'incomes' => $incomes->items()
        ]);
    }
}
