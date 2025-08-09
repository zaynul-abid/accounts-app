<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = Expense::with(['expenseType', 'createdBy', 'bankAccount', 'supplier'])
            ->where('company_id', $companyId);

        // Filter by current day unless show_all is requested
        if (!$request->has('show_all') || !$request->input('show_all')) {
            $query->whereDate('date_time', Carbon::today());
        }

        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('voucher_number', 'like', "%{$searchTerm}%")
                    ->orWhere('reference_note', 'like', "%{$searchTerm}%")
                    ->orWhere('narration', 'like', "%{$searchTerm}%")
                    ->orWhereHas('expenseType', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $expenses = $query->latest()->paginate(10)->appends($request->except('page'));

        $expenseTypes = ExpenseType::where('company_id', $companyId)->get();
        $bankAccounts = BankAccount::where('company_id', $companyId)
            ->where('is_active', 1)
            ->get();
        $suppliers = Supplier::where('company_id', $companyId)
            ->where('status', 1)
            ->get();

        if ($request->ajax()) {
            return view('frontend.pages.expenses.index', compact('expenses', 'expenseTypes', 'bankAccounts', 'suppliers'))->render();
        }

        return view('frontend.pages.expenses.index', compact('expenses', 'expenseTypes', 'bankAccounts', 'suppliers'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'expense_type_id' => 'required|exists:expense_types,id',
                'voucher_number' => 'required|string|max:255',
                'reference_note' => 'nullable|string|max:255',
                'bank_account_id' => 'required_if:payment_mode,bank|exists:bank_accounts,id|nullable',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'date_time' => 'required|date',
                'payment_mode' => 'required|in:cash,bank,credit,touch&go,boost,duitinow',
                'payment_amount' => 'required|numeric|min:0',
                'narration' => 'nullable|string',
            ]);

            // Get the authenticated user and company ID
            $user = auth()->user();
            if (!$user->company_id) {
                return $request->wantsJson() || $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'User is not associated with any company'], 403)
                    : redirect()->back()->withErrors(['error' => 'User is not associated with any company']);
            }

            // Start a database transaction
            return DB::transaction(function () use ($request, $validated, $user) {
                // Create expense
                $expense = Expense::create(array_merge($validated, [
                    'created_by' => $user->id,
                    'company_id' => $user->company_id,
                ]));

                // Create supplier transaction if supplier_id is provided
                if ($validated['supplier_id']) {
                    $transactionType = $validated['payment_mode'] === 'credit' ? 'purchase' : 'payment';
                    $amountField = $validated['payment_mode'] === 'credit' ? 'credit' : 'debit';

                    SupplierTransaction::create([
                        'company_id' => $user->company_id,
                        'supplier_id' => $validated['supplier_id'],
                        'date' => $validated['date_time'],
                        'bill_number' => $validated['voucher_number'],
                        'transaction_mode' => $expense->expenseType->name,
                        'expense_id' => $expense->id,
                        'transaction_type' => $transactionType,
                        'debit' => $amountField === 'debit' ? $validated['payment_amount'] : 0,
                        'credit' => $amountField === 'credit' ? $validated['payment_amount'] : 0,
                        'notes' => $validated['narration'] ?? 'Expense transaction',
                    ]);
                }

                // Create transaction entry
                Transaction::create([
                    'company_id' => $user->company_id,
                    'date' => \Carbon\Carbon::parse($validated['date_time'])->toDateString(),
                    'transaction_mode' => 'expense',
                    'income_id' => null,
                    'expense_id' => $expense->id,
                    'transaction_type' => $expense->expenseType->name,
                    'payment_mode' => $validated['payment_mode'],
                    'bank_id' => $validated['bank_account_id'],
                    'debit' => '0',
                    'credit' => $validated['payment_amount'],
                    'created_by' => $user->id,
                ]);

                // Return response based on request type
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Expense and transaction recorded successfully',
                        'expense' => $expense->load('expenseType', 'createdBy', 'bankAccount', 'supplier'),
                    ], 201);
                }

                return redirect()->route('expenses.index')
                    ->with('success', 'Expense and transaction recorded successfully!');
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to record expense or transaction: ' . $e->getMessage());

            // Return error response based on request type
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Failed to record expense or transaction: ' . $e->getMessage()], 500)
                : redirect()->back()->withErrors(['error' => 'Failed to record expense or transaction']);
        }
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
            'payment_mode' => 'required|in:cash,bank,credit,touch&go,boost,duitinow',
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
                    'company_id' => $expense->company_id,
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

    // ❗ Delete related transaction entry
    Transaction::where('expense_id', $expense->id)->delete();

    // Delete the expense
    $expense->delete();

    $page = request()->input('page', 1);
    $showAll = request()->input('show_all', 0);
    $companyId = auth()->user()->company_id;

    $query = Expense::with(['expenseType', 'createdBy', 'bankAccount', 'supplier'])
        ->where('company_id', $companyId);

    if (!$showAll) {
        $query->whereDate('date_time', Carbon::today());
    }

    $expenses = $query->latest()->paginate(10, ['*'], 'page', $page);

    if ($expenses->isEmpty() && $page > 1) {
        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
            'redirect' => true,
            'redirectUrl' => route('expenses.index', ['page' => $page - 1, 'show_all' => $showAll])
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Expense deleted successfully',
        'expenses' => $expenses->items()
    ]);
}

}