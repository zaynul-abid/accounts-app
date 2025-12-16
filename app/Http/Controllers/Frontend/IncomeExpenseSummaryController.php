<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class IncomeExpenseSummaryController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $today = Carbon::today('Asia/Kolkata')->toDateString();

        $expenses = Expense::with(['expenseType', 'createdBy'])
            ->where('company_id', $companyId)
            ->whereDate('date_time', $today)
            ->latest()
            ->paginate(10);

        $incomes = Income::with(['incomeType', 'createdBy'])
            ->where('company_id', $companyId)
            ->whereDate('date_time', $today)
            ->latest()
            ->paginate(10);

        return view('frontend.pages.income-expense-summary.index',
            compact('expenses', 'incomes'));
    }

   public function getData(Request $request)
{
    $type = $request->query('type', 'all');
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $incomePage = $request->query('income_page', 1);
    $expensePage = $request->query('expense_page', 1);
    $perPage = 10;

    $companyId = auth()->user()->company_id;

    $incomeQuery = Income::with(['incomeType', 'createdBy'])
        ->where('company_id', $companyId);

    $expenseQuery = Expense::with(['expenseType', 'createdBy'])
        ->where('company_id', $companyId);

    // Apply date filters
       if ($startDate && $endDate) {
           $start = Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay();
           $end = Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay();
           $incomeQuery->whereBetween('date_time', [$start, $end]);
           $expenseQuery->whereBetween('date_time', [$start, $end]);
       } elseif ($startDate) {
           $start = Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay();
           $incomeQuery->where('date_time', '>=', $start);
           $expenseQuery->where('date_time', '>=', $start);
       } elseif ($endDate) {
           $end = Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay();
           $incomeQuery->where('date_time', '<=', $end);
           $expenseQuery->where('date_time', '<=', $end);
       }
    // else → no filter, fetch all data (do nothing)

    // Type filter
    if ($type === 'income') {
        $expenseQuery->whereRaw('1 = 0'); // empty expense
    } elseif ($type === 'expense') {
        $incomeQuery->whereRaw('1 = 0'); // empty income
    }

    // Sorting
    $incomeSortColumn = $request->query('income_sort_column', 'date_time');
    $incomeSortOrder = $request->query('income_sort_order', 'desc');
    $expenseSortColumn = $request->query('expense_sort_column', 'date_time');
    $expenseSortOrder = $request->query('expense_sort_order', 'desc');

    $incomeQuery->orderBy(
        $incomeSortColumn === 'amount' ? 'receipt_amount' :
        ($incomeSortColumn === 'payment_mode' ? 'payment_mode' : 'date_time'),
        $incomeSortOrder
    );

    $expenseQuery->orderBy(
        $expenseSortColumn === 'amount' ? 'payment_amount' :
        ($expenseSortColumn === 'payment_mode' ? 'payment_mode' : 'date_time'),
        $expenseSortOrder
    );

    // Clone queries BEFORE pagination for totals
    $incomeTotalQuery = clone $incomeQuery;
    $expenseTotalQuery = clone $expenseQuery;

    // Paginate for table display
    $incomes = $incomeQuery->paginate($perPage, ['*'], 'income_page', $incomePage);
    $expenses = $expenseQuery->paginate($perPage, ['*'], 'expense_page', $expensePage);

    // Calculate totals from full filtered dataset
    $totalIncome = $incomeTotalQuery->sum('receipt_amount');
    $totalExpense = $expenseTotalQuery->sum('payment_amount');
    $balance = $totalIncome - $totalExpense;

    $openingBalanceRecord = OpeningBalance::first();
    $openingBalance = $openingBalanceRecord ? $openingBalanceRecord->opening_balance : 0;
    $netBalance = $openingBalance + $balance;

    // Build income table rows
    $incomeRows = '';
    foreach ($incomes as $index => $income) {
        $rowNumber = $incomes->firstItem() + $index;
        $incomeRows .= '<tr class="hover:bg-gray-50">';
        $incomeRows .= '<td class="px-4 py-3">' . $rowNumber . '</td>';
        $incomeRows .= '<td class="px-4 py-3">' . \Carbon\Carbon::parse($income->date_time, 'Asia/Kolkata')->format('Y-m-d') . '</td>';
        $incomeRows .= '<td class="px-4 py-3">' . $income->incomeType->name . '</td>';
        $incomeRows .= '<td class="px-4 py-3">' . ($income->receipt_mode ?? 'N/A') . '</td>';
        $incomeRows .= '<td class="px-4 py-3 text-right text-green-600">' . $income->receipt_amount . '</td>';
        $incomeRows .= '</tr>';
    }

    // Build expense table rows
    $expenseRows = '';
    foreach ($expenses as $index => $expense) {
        $rowNumber = $expenses->firstItem() + $index;
        $expenseRows .= '<tr class="hover:bg-gray-50">';
        $expenseRows .= '<td class="px-4 py-3">' . $rowNumber . '</td>';
        $expenseRows .= '<td class="px-4 py-3">' . \Carbon\Carbon::parse($expense->date_time, 'Asia/Kolkata')->format('Y-m-d') . '</td>';
        $expenseRows .= '<td class="px-4 py-3">' . $expense->expenseType->name . '</td>';
        $expenseRows .= '<td class="px-4 py-3">' . ($expense->payment_mode ?? 'N/A') . '</td>';
        $expenseRows .= '<td class="px-4 py-3 text-right text-red-600">' . $expense->payment_amount . '</td>';
        $expenseRows .= '</tr>';
    }

    // Pagination HTML
    $incomePagination = $incomes->appends([
        'type' => $type,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'income_sort_column' => $incomeSortColumn,
        'income_sort_order' => $incomeSortOrder,
        'expense_sort_column' => $expenseSortColumn,
        'expense_sort_order' => $expenseSortOrder,
        'expense_page' => $expensePage
    ])->links('pagination::custom-simple')->toHtml();

    $expensePagination = $expenses->appends([
        'type' => $type,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'income_sort_column' => $incomeSortColumn,
        'income_sort_order' => $incomeSortOrder,
        'expense_sort_column' => $expenseSortColumn,
        'expense_sort_order' => $expenseSortOrder,
        'income_page' => $incomePage
    ])->links('pagination::custom-simple')->toHtml();

    return response()->json([
        'incomes' => $incomeRows,
        'expenses' => $expenseRows,
        'income_pagination' => $incomePagination,
        'expense_pagination' => $expensePagination,
        'total_income' => number_format($totalIncome, 2),
        'total_expense' => number_format($totalExpense, 2),
        'balance' => number_format($balance, 2),
        'opening_balance' => number_format($openingBalance, 2),
        'net_balance' => number_format($netBalance, 2)
    ]);
}
/**
     * Exports the Income/Expense summary to a PDF based on current filters.
     */
    public function exportPdf(Request $request)
    {
        $type = $request->query('type', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $companyId = auth()->user()->company_id;

        // 1. Setup Base Queries
        $incomeQuery = Income::with(['incomeType', 'createdBy'])
            ->where('company_id', $companyId);

        $expenseQuery = Expense::with(['expenseType', 'createdBy'])
            ->where('company_id', $companyId);

        // 2. Apply Date Filters (Same logic as getData)
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay();
            $end = Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay();
            $incomeQuery->whereBetween('date_time', [$start, $end]);
            $expenseQuery->whereBetween('date_time', [$start, $end]);
        } elseif ($startDate) {
            $start = Carbon::parse($startDate, 'Asia/Kolkata')->startOfDay();
            $incomeQuery->where('date_time', '>=', $start);
            $expenseQuery->where('date_time', '>=', $start);
        } elseif ($endDate) {
            $end = Carbon::parse($endDate, 'Asia/Kolkata')->endOfDay();
            $incomeQuery->where('date_time', '<=', $end);
            $expenseQuery->where('date_time', '<=', $end);
        }

        // 3. Apply Type Filters (Same logic as getData)
        if ($type === 'income') {
            $expenseQuery->whereRaw('1 = 0'); // Empty expense list
        } elseif ($type === 'expense') {
            $incomeQuery->whereRaw('1 = 0'); // Empty income list
        }

        // 4. Fetch ALL Data (No Pagination)
        // We'll use the sorting from the request just in case, but PDF reports often use simple date sorting.
        $incomeSortColumn = $request->query('income_sort_column', 'date_time');
        $incomeSortOrder = $request->query('income_sort_order', 'desc');
        $expenseSortColumn = $request->query('expense_sort_column', 'date_time');
        $expenseSortOrder = $request->query('expense_sort_order', 'desc');

        $incomes = $incomeQuery->orderBy(
            $incomeSortColumn === 'amount' ? 'receipt_amount' :
            ($incomeSortColumn === 'payment_mode' ? 'payment_mode' : 'date_time'),
            $incomeSortOrder
        )->get();

        $expenses = $expenseQuery->orderBy(
            $expenseSortColumn === 'amount' ? 'payment_amount' :
            ($expenseSortColumn === 'payment_mode' ? 'payment_mode' : 'date_time'),
            $expenseSortOrder
        )->get();

        // 5. Calculate Summary Totals (Same logic as getData)
        $totalIncome = $incomes->sum('receipt_amount');
        $totalExpense = $expenses->sum('payment_amount');
        $balance = $totalIncome - $totalExpense;

        $openingBalanceRecord = OpeningBalance::first();
        $openingBalance = $openingBalanceRecord ? $openingBalanceRecord->opening_balance : 0;
        $netBalance = $openingBalance + $balance;

        // 6. Report Title Logic
        $reportTitle = $this->getReportTitle($type, $startDate, $endDate);

        // 7. Load View and Download PDF
        $pdf = Pdf::loadView('frontend.pages.income-expense-summary.pdf', [ // Use a new view for PDF
            'incomes' => $incomes,
            'expenses' => $expenses,
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalIncome' => number_format($totalIncome, 2),
            'totalExpense' => number_format($totalExpense, 2),
            'balance' => number_format($balance, 2),
            'openingBalance' => number_format($openingBalance, 2),
            'netBalance' => number_format($netBalance, 2),
            'currency' => '₹', // Assuming Indian Rupee based on time zone
        ]);

        return $pdf->download($reportTitle . '.pdf');
    }

    /**
     * Helper function to generate dynamic title for PDF file
     */
    protected function getReportTitle(string $type, ?string $startDate, ?string $endDate): string
    {
        $typeMap = ['all' => 'Summary', 'income' => 'Income', 'expense' => 'Expense'];
        $title = $typeMap[$type] ?? 'Summary';

        if ($startDate && $endDate) {
            if ($startDate === $endDate) {
                $datePart = '_On_' . $startDate;
            } else {
                $datePart = '_From_' . $startDate . '_To_' . $endDate;
            }
        } elseif (!$startDate && !$endDate) {
            $datePart = '_All_Time';
        } else {
            $datePart = ''; // Should not happen with current logic, but keeps it safe
        }

        return str_replace(' ', '_', $title) . '_Report' . $datePart;
    }

}
