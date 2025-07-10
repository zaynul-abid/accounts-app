<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\OpeningBalance;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminIndex()
    {
        $companyId = Auth::user()->company_id;

        $totalIncome = Income::where('company_id', $companyId)->sum('receipt_amount');
        $totalExpense = Expense::where('company_id', $companyId)->sum('payment_amount');
        $openingBalance = OpeningBalance::where('company_id', $companyId)->value('opening_balance');
        $suppliers = Supplier::where('company_id', $companyId)->count();



        $balance = ($totalIncome - $totalExpense) + $openingBalance;

        return view('backend.pages.dashboard.index', compact('totalIncome', 'totalExpense', 'balance', 'openingBalance','suppliers'));
    }
    public function superAdminIndex(){
        return view('backend.pages.dashboard.index');
    }

    public function employeeIndex(){
        return view('frontend.pages.employee-dashboard.index');
    }
}
