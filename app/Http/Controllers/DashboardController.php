<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\OpeningBalance;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function adminIndex()
    {
        // Ensure user is authenticated and has a company_id
        if (!Auth::check() || !Auth::user()->company_id) {
            return redirect()->route('login')->withErrors(['error' => 'User is not authenticated or not associated with a company']);
        }

        $companyId = Auth::user()->company_id;
        $today = Carbon::today();

        // TOTALS
        $totalIncome = Income::where('company_id', $companyId)->sum('receipt_amount');
        $totalExpense = Expense::where('company_id', $companyId)->sum('payment_amount');
        $openingBalance = OpeningBalance::where('company_id', $companyId)->value('opening_balance') ?? 0;
        $suppliers = Supplier::where('company_id', $companyId)->count();
        $balance = ($totalIncome - $totalExpense) + $openingBalance;

        // TODAY'S ONLY
        $todayIncome = Income::where('company_id', $companyId)
            ->whereDate('date_time', $today)
            ->sum('receipt_amount');

        $todayExpense = Expense::where('company_id', $companyId)
            ->whereDate('date_time', $today)
            ->sum('payment_amount');


        $todayBalance = ($todayIncome - $todayExpense);

        // Mode Balances (touch&go, boost, duitinow)
        $paymentModes = ['touch&go', 'boost', 'duitinow'];
        $modeBalances = [];

        foreach ($paymentModes as $mode) {
            $result = Transaction::where('payment_mode', $mode)
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();

            $totalDebit = $result->total_debit ?? 0;
            $totalCredit = $result->total_credit ?? 0;

            $modeBalances[$mode] = $totalDebit - $totalCredit;
        }

        // Cash Balance
        $cashResult = Transaction::where('payment_mode', 'cash')
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $cashBalance = ($cashResult->total_debit ?? 0) - ($cashResult->total_credit ?? 0);

        // Credit Balance
        $creditResult = Transaction::where('payment_mode', 'credit')
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $creditBalance = ($creditResult->total_debit ?? 0) - ($creditResult->total_credit ?? 0);

        // Bank Balances (per bank_id)
        $bankBalances = Transaction::where('payment_mode', 'bank')
            ->whereNotNull('bank_id')
            ->groupBy('bank_id')
            ->select(
                'bank_id',
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(credit), 0) as total_credit')
            )
            ->with(['bank' => function ($query) {
                $query->select('id', 'account_name');
            }])
            ->get()
            ->mapWithKeys(function ($item) {
                $balance = ($item->total_debit ?? 0) - ($item->total_credit ?? 0);

                return [$item->bank_id => [
                    'name' => $item->bank->account_name ?? 'Unknown Bank',
                    'balance' => $balance,
                ]];
            });

        // Return view
        return view('backend.pages.dashboard.index', compact(
            'totalIncome',
            'totalExpense',
            'openingBalance',
            'suppliers',
            'balance',
            'todayIncome',
            'todayExpense',
            'todayBalance',
            'modeBalances',
            'cashBalance',
            'creditBalance',
            'bankBalances'
        ));
    }

    public function superAdminIndex()
    {
        return view('backend.pages.dashboard.index');
    }

    public function employeeIndex()
    {
        return view('frontend.pages.employee-dashboard.index');
    }
}
