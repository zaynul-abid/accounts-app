<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Income::with(['incomeType', 'createdBy', 'bankAccount'])
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
                    ->orWhereHas('incomeType', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $incomes = $query->latest()->paginate(10)->appends($request->except('page'));

        // Sort income types alphabetically
        $incomeTypes = IncomeType::where('company_id', $companyId)
            ->orderBy('name', 'asc')
            ->get();

        // Sort bank accounts alphabetically
        $bankAccounts = BankAccount::where('company_id', $companyId)
            ->where('is_active', 1)
            ->orderBy('account_name', 'asc')
            ->get();

        if ($request->ajax()) {
            return view('frontend.pages.incomes.index', compact('incomes', 'incomeTypes', 'bankAccounts'))->render();
        }

        return view('frontend.pages.incomes.index', compact('incomes', 'incomeTypes', 'bankAccounts'));
    }


    public function store(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'income_type_id' => 'required|exists:income_types,id',
                'voucher_number' => 'required|string|max:255',
                'reference_note' => 'nullable|string|max:255',
                'bank_account_id' => 'required_if:receipt_mode,bank|exists:bank_accounts,id|nullable',
                'date_time' => 'required|date',
                'receipt_mode' => 'required|in:cash,bank,credit,touch&go,boost,duitinow',
                'receipt_amount' => 'required|numeric|min:0',
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
                // Create income
                $income = Income::create(array_merge($validated, [
                    'created_by' => $user->id,
                    'company_id' => $user->company_id,
                ]));

                // Create transaction entry
                Transaction::create([
                    'company_id' => $user->company_id,
                    'date' => \Carbon\Carbon::parse($validated['date_time'])->toDateString(),
                    'transaction_mode' => 'income',
                    'income_id' => $income->id,
                    'expense_id' => null,
                    'transaction_type' => $income->incomeType->name,
                    'payment_mode' => $validated['receipt_mode'],
                    'bank_id' => $validated['bank_account_id'],
                    'debit' => $validated['receipt_amount'],
                    'credit' => '0',
                    'created_by' => $user->id,
                ]);

                // Return response based on request type
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Income and transaction recorded successfully',
                        'income' => $income->load('incomeType', 'createdBy', 'bankAccount'),
                    ], 201);
                }

                return redirect()->route('incomes.index')
                    ->with('success', 'Income and transaction recorded successfully!');
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to record income or transaction: ' . $e->getMessage());

            // Return error response based on request type
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Failed to record income or transaction: ' . $e->getMessage()], 500)
                : redirect()->back()->withErrors(['error' => 'Failed to record income or transaction']);
        }
    }

    public function edit(Income $income)
    {
        return response()->json([
            'income' => $income->load('incomeType', 'bankAccount')
        ]);
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'income_type_id' => 'required|exists:income_types,id',
            'voucher_number' => 'required|string|max:255',
            'reference_note' => 'nullable|string|max:255',
            'bank_account_id' => 'required_if:receipt_mode,bank|exists:bank_accounts,id|nullable',
            'date_time' => 'required|date',
            'receipt_mode' => 'required|in:cash,bank,credit,touch&go,boost,duitinow',
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

   public function destroy(Income $income)
{
    // ❗ Delete related transaction entry
    Transaction::where('income_id', $income->id)->delete();

    // Delete the income entry
    $income->delete();

    $page = request()->input('page', 1);
    $showAll = request()->input('show_all', 0);
    $companyId = auth()->user()->company_id;

    $query = Income::with(['incomeType', 'createdBy', 'bankAccount'])
        ->where('company_id', $companyId);

    if (!$showAll) {
        $query->whereDate('date_time', Carbon::today());
    }

    $incomes = $query->latest()->paginate(10, ['*'], 'page', $page);

    if ($incomes->isEmpty() && $page > 1) {
        return response()->json([
            'success' => true,
            'message' => 'Income deleted successfully',
            'redirect' => true,
            'redirectUrl' => route('incomes.index', ['page' => $page - 1, 'show_all' => $showAll])
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Income deleted successfully',
        'incomes' => $incomes->items()
    ]);
}

}
