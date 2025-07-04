<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['expenseType', 'createdBy', 'bankAccount', 'supplier'])
            ->latest()
            ->paginate(10);
        $expenseTypes = ExpenseType::all();
        $bankAccounts = BankAccount::where('is_active', 1)->get();
        $suppliers = Supplier::where('status', 1)->get();
        return view('frontend.pages.expenses.index', compact('expenses', 'expenseTypes', 'bankAccounts', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:payment_mode,bank|exists:bank_accounts,id|nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_time' => 'required|date',
            'payment_mode' => 'required|in:cash,credit,bank',
            'payment_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        // Create expense
        $expense = Expense::create($validated + ['created_by' => auth()->id()]);

        // Create supplier transaction if supplier is selected
        if ($validated['supplier_id']) {
            $transactionType = ($validated['payment_mode'] === 'credit') ? 'purchase' : 'payment';
            $amountField = ($validated['payment_mode'] === 'credit') ? 'credit' : 'debit';

            SupplierTransaction::create([
                'supplier_id' => $validated['supplier_id'],
                'date' => $validated['date_time'],
                'bill_number' => $validated['voucher_number'],
                'transaction_mode' => $expense->expenseType->name,
                'expense_id' => $expense->id,
                'transaction_type' => $transactionType,
                'debit' => $amountField === 'debit' ? $validated['payment_amount'] : 0,
                'credit' => $amountField === 'credit' ? $validated['payment_amount'] : 0,
                'notes' => $validated['narration'] ?? 'Expense transaction'
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense recorded successfully',
                'expense' => $expense->load('expenseType', 'createdBy', 'bankAccount', 'supplier')
            ]);
        }

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    public function edit(Expense $expense)
    {
        return response()->json([
            'expense' => $expense->load('expenseType', 'bankAccount', 'supplier')
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:payment_mode,bank|exists:bank_accounts,id|nullable',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'date_time' => 'required|date',
            'payment_mode' => 'required|in:cash,credit,bank',
            'payment_amount' => 'required|numeric|min:0',
            'narration' => 'nullable|string',
        ]);

        // Update expense
        $expense->update($validated);

        // Update or create supplier transaction if supplier is selected
        if ($validated['supplier_id']) {
            $transactionType = ($validated['payment_mode'] === 'credit') ? 'purchase' : 'payment';
            $amountField = ($validated['payment_mode'] === 'credit') ? 'credit' : 'debit';

            $transaction = SupplierTransaction::where('expense_id', $expense->id)
                ->where('supplier_id', $validated['supplier_id'])
                ->first();

            if ($transaction) {
                $transaction->update([
                    'date' => $validated['date_time'],
                    'bill_number' => $validated['voucher_number'],
                    'transaction_mode' => $expense->expenseType->name,
                    'transaction_type' => $transactionType,
                    'debit' => $amountField === 'debit' ? $validated['payment_amount'] : 0,
                    'credit' => $amountField === 'credit' ? $validated['payment_amount'] : 0,
                    'notes' => $validated['narration'] ?? 'Updated expense transaction'
                ]);
            } else {
                SupplierTransaction::create([
                    'supplier_id' => $validated['supplier_id'],
                    'date' => $validated['date_time'],
                    'bill_number' => $validated['voucher_number'],
                    'transaction_mode' => $expense->expenseType->name,
                    'expense_id' => $expense->id,
                    'transaction_type' => $transactionType,
                    'debit' => $amountField === 'debit' ? $validated['payment_amount'] : 0,
                    'credit' => $amountField === 'credit' ? $validated['payment_amount'] : 0,
                    'notes' => $validated['narration'] ?? 'Expense transaction'
                ]);
            }
        } else {
            // If supplier_id is removed, delete related transaction
            SupplierTransaction::where('expense_id', $expense->id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'expense' => $expense->load('expenseType', 'createdBy', 'bankAccount', 'supplier')
        ]);
    }

    public function destroy(Expense $expense)
    {
        // Delete related supplier transaction
        SupplierTransaction::where('expense_id', $expense->id)->delete();

        $expense->delete();

        $page = request()->input('page', 1);
        $expenses = Expense::with(['expenseType', 'createdBy', 'bankAccount', 'supplier'])
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
