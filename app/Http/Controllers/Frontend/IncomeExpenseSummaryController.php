<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncomeExpenseSummaryController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['expenseType', 'createdBy'])
            ->latest()
            ->paginate(10);

        $incomes = Income::with(['incomeType', 'createdBy'])
            ->latest()
            ->paginate(10);

        return view('frontend.pages.income-expense-summary.index', compact('expenses', 'incomes'));
    }

    public function getData(Request $request)
    {
        $type = $request->query('type', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Build queries
        $incomeQuery = Income::with(['incomeType', 'createdBy']);
        $expenseQuery = Expense::with(['expenseType', 'createdBy']);

        // Apply date filters only if both dates are provided
        if ($startDate && $endDate) {
            $incomeQuery->whereBetween('date_time', [$startDate, $endDate]);
            $expenseQuery->whereBetween('date_time', [$startDate, $endDate]);
        }

        // Apply type filter
        if ($type === 'income') {
            $expenseQuery->whereRaw('1 = 0'); // Return empty expenses
        } elseif ($type === 'expense') {
            $incomeQuery->whereRaw('1 = 0'); // Return empty incomes
        }

        // Apply sorting
        $incomeSortColumn = $request->query('income_sort_column', 'date_time');
        $incomeSortOrder = $request->query('income_sort_order', 'desc');
        $expenseSortColumn = $request->query('expense_sort_column', 'date_time');
        $expenseSortOrder = $request->query('expense_sort_order', 'desc');

        $incomeQuery->orderBy($incomeSortColumn === 'amount' ? 'receipt_amount' : 'date_time', $incomeSortOrder);
        $expenseQuery->orderBy($expenseSortColumn === 'amount' ? 'payment_amount' : 'date_time', $expenseSortOrder);

        // Get data
        $incomes = $incomeQuery->get();
        $expenses = $expenseQuery->get();

        // Calculate totals
        $totalIncome = $incomes->sum('receipt_amount');
        $totalExpense = $expenses->sum('payment_amount');
        $balance = $totalIncome - $totalExpense;

        // Fetch opening balance
        $openingBalanceRecord = OpeningBalance::first();
        $openingBalance = $openingBalanceRecord ? $openingBalanceRecord->opening_balance : 0;

        // Calculate net balance
        $netBalance = $openingBalance + $balance;

        // Render table rows
        $incomeRows = '';
        foreach ($incomes as $income) {
            $incomeRows .= '<tr class="hover:bg-gray-50">';
            $incomeRows .= '<td class="px-4 py-3">' . \Carbon\Carbon::parse($income->date_time)->format('Y-m-d') . '</td>';
            $incomeRows .= '<td class="px-4 py-3">' . $income->incomeType->name . '</td>';
            $incomeRows .= '<td class="px-4 py-3 text-right text-green-600">' . $income->receipt_amount . '</td>';
            $incomeRows .= '</tr>';
        }

        $expenseRows = '';
        foreach ($expenses as $expense) {
            $expenseRows .= '<tr class="hover:bg-gray-50">';
            $expenseRows .= '<td class="px-4 py-3">' . \Carbon\Carbon::parse($expense->date_time)->format('Y-m-d') . '</td>';
            $expenseRows .= '<td class="px-4 py-3">' . $expense->expenseType->name . '</td>';
            $expenseRows .= '<td class="px-4 py-3 text-right text-red-600">' . $expense->payment_amount . '</td>';
            $expenseRows .= '</tr>';
        }

        return response()->json([
            'incomes' => $incomeRows,
            'expenses' => $expenseRows,
            'total_income' => number_format($totalIncome, 2),
            'total_expense' => number_format($totalExpense, 2),
            'balance' => number_format($balance, 2),
            'opening_balance' => number_format($openingBalance, 2),
            'net_balance' => number_format($netBalance, 2)
        ]);
    }
}
