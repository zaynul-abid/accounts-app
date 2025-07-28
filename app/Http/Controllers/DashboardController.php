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

class DashboardController extends Controller
{
    public function adminIndex()
    {
        // Ensure user is authenticated and has a company_id
        if (!Auth::check() || !Auth::user()->company_id) {
            return redirect()->route('login')->withErrors(['error' => 'User is not authenticated or not associated with a company']);
        }

        $companyId = Auth::user()->company_id;

        // Existing calculations
        $totalIncome = Income::where('company_id', $companyId)->sum('receipt_amount');
        $totalExpense = Expense::where('company_id', $companyId)->sum('payment_amount');
        $openingBalance = OpeningBalance::where('company_id', $companyId)->value('opening_balance') ?? 0;
        $suppliers = Supplier::where('company_id', $companyId)->count();
        $balance = ($totalIncome - $totalExpense) + $openingBalance;

        // Calculate balances for touch&go, boost, and duitinow
        $paymentModes = ['touch&go', 'boost', 'duitinow'];
        $modeBalances = [];
        foreach ($paymentModes as $mode) {
            $result = Transaction::where('payment_mode', $mode)
                ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
                ->first();
            $totalDebit = $result->total_debit ?? 0;
            $totalCredit = $result->total_credit ?? 0;

            if ($totalDebit > 0 && $totalCredit > 0) {
                $modeBalances[$mode] = $totalDebit - $totalCredit;
            } elseif ($totalDebit > 0) {
                $modeBalances[$mode] = $totalDebit;
            } elseif ($totalCredit > 0) {
                $modeBalances[$mode] = -$totalCredit;
            } else {
                $modeBalances[$mode] = 0;
            }
        }

        // Calculate balance for cash
        $cashResult = Transaction::where('payment_mode', 'cash')
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();
        $cashDebit = $cashResult->total_debit ?? 0;
        $cashCredit = $cashResult->total_credit ?? 0;

        if ($cashDebit > 0 && $cashCredit > 0) {
            $cashBalance = $cashDebit - $cashCredit;
        } elseif ($cashDebit > 0) {
            $cashBalance = $cashDebit;
        } elseif ($cashCredit > 0) {
            $cashBalance = -$cashCredit;
        } else {
            $cashBalance = 0;
        }

        // Calculate balance for credit
        $creditResult = Transaction::where('payment_mode', 'credit')
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();
        $creditDebit = $creditResult->total_debit ?? 0;
        $creditCredit = $creditResult->total_credit ?? 0;

        if ($creditDebit > 0 && $creditCredit > 0) {
            $creditBalance = $creditDebit - $creditCredit;
        } elseif ($creditDebit > 0) {
            $creditBalance = $creditDebit;
        } elseif ($creditCredit > 0) {
            $creditBalance = -$creditCredit;
        } else {
            $creditBalance = 0;
        }

        // Calculate balances for each bank account
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
                $totalDebit = $item->total_debit ?? 0;
                $totalCredit = $item->total_credit ?? 0;
                $balance = 0;

                if ($totalDebit > 0 && $totalCredit > 0) {
                    $balance = $totalDebit - $totalCredit;
                } elseif ($totalDebit > 0) {
                    $balance = $totalDebit;
                } elseif ($totalCredit > 0) {
                    $balance = -$totalCredit;
                }

                return [$item->bank_id => [
                    'name' => $item->bank->account_name ?? 'Unknown Bank',
                    'balance' => $balance,
                ]];
            });

        // Return the view with all calculated values
        return view('backend.pages.dashboard.index', compact(
            'totalIncome',
            'totalExpense',
            'openingBalance',
            'suppliers',
            'balance',
            'modeBalances',
            'cashBalance',
            'creditBalance',
            'bankBalances'
        ));
    }
    public function superAdminIndex(){
        return view('backend.pages.dashboard.index');
    }

    public function employeeIndex(){
        return view('frontend.pages.employee-dashboard.index');
    }
}
