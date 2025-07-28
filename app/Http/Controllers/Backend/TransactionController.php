<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->company_id) {
            return redirect()->route('login')->withErrors(['error' => 'User is not authenticated or not associated with a company']);
        }

        $companyId = Auth::user()->company_id;

        // Validate and handle custom date filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Base query
        $query = Transaction::where('transactions.company_id', $companyId);

        if ($startDate && $endDate) {
            Log::info('Filtering transactions between ' . $startDate . ' and ' . $endDate);
            $query->whereBetween('transactions.date', [$startDate, $endDate]);
        }

        // Calculate balances for all payment modes
        $paymentModes = ['touch&go', 'boost', 'duitinow', 'cash', 'credit', 'bank'];
        $balances = [];

        foreach ($paymentModes as $mode) {
            $result = (clone $query)->where('payment_mode', $mode)
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $totalDebit = $result->total_debit ?? 0;
            $totalCredit = $result->total_credit ?? 0;
            $balances[$mode] = $totalDebit - $totalCredit;
            Log::info("Balance for $mode: debit=$totalDebit, credit=$totalCredit, balance=" . ($totalDebit - $totalCredit)); // Debug balance
        }

        // Get individual bank accounts and their balances
        $banks = BankAccount::where('company_id', $companyId)->select('id', 'account_name')->get();
        $bankBalances = [];

        foreach ($banks as $bank) {
            $bankResult = (clone $query)->where('payment_mode', 'bank')
                ->where('transactions.bank_id', $bank->id)
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $totalDebit = $bankResult->total_debit ?? 0;
            $totalCredit = $bankResult->total_credit ?? 0;
            $bankBalances[$bank->id] = [
                'name' => $bank->account_name,
                'balance' => $totalDebit - $totalCredit,
            ];
            Log::info("Bank {$bank->account_name} balance: debit=$totalDebit, credit=$totalCredit, balance=" . ($totalDebit - $totalCredit)); // Debug bank balance
        }

        // Handle AJAX request for transaction details
        if ($request->ajax()) {
            $selectedMode = $request->input('mode');
            $selectedBankId = $request->input('bank_id');
            $allBanksSelected = $request->input('all_banks') == 1;

            $transactionsQuery = (clone $query);
            $selectedName = '';
            $selectedBalance = 0;
            $hasBankColumn = false;

            if ($allBanksSelected) {
                $transactionsQuery->where('payment_mode', 'bank')
                    ->join('bank_accounts', 'transactions.bank_id', '=', 'bank_accounts.id')
                    ->select('transactions.*', 'bank_accounts.account_name as bank_name');
                $selectedName = 'All Banks';
                $selectedBalance = $balances['bank'];
                $hasBankColumn = true;
            } elseif ($selectedMode) {
                $transactionsQuery->where('payment_mode', $selectedMode);
                $selectedName = ucwords(str_replace('&', ' & ', $selectedMode));
                $selectedBalance = $balances[$selectedMode] ?? 0;
            } elseif ($selectedBankId) {
                $transactionsQuery->where('payment_mode', 'bank')
                    ->where('transactions.bank_id', $selectedBankId)
                    ->join('bank_accounts', 'transactions.bank_id', '=', 'bank_accounts.id')
                    ->select('transactions.*', 'bank_accounts.account_name as bank_name');
                $selectedBank = BankAccount::find($selectedBankId);
                $selectedName = $selectedBank ? $selectedBank->account_name : 'Unknown Bank';
                $selectedBalance = $bankBalances[$selectedBankId]['balance'] ?? 0;
                $hasBankColumn = true;
            }

            $transactions = $transactionsQuery->where(function($q) {
                $q->whereNotNull('debit')->orWhereNotNull('credit');
            })
                ->orderBy('transactions.date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'selectedName' => $selectedName,
                'selectedBalance' => $selectedBalance,
                'hasBankColumn' => $hasBankColumn,
                'modeBalances' => [
                    'touch&go' => $balances['touch&go'],
                    'boost' => $balances['boost'],
                    'duitinow' => $balances['duitinow'],
                ],
                'cashBalance' => $balances['cash'],
                'creditBalance' => $balances['credit'],
                'allBanksBalance' => $balances['bank'],
                'bankBalances' => $bankBalances,
            ]);
        }

        return view('backend.pages.transactions.index', [
            'modeBalances' => [
                'touch&go' => $balances['touch&go'],
                'boost' => $balances['boost'],
                'duitinow' => $balances['duitinow'],
            ],
            'cashBalance' => $balances['cash'],
            'creditBalance' => $balances['credit'],
            'allBanksBalance' => $balances['bank'],
            'banks' => $banks,
            'bankBalances' => $bankBalances,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
