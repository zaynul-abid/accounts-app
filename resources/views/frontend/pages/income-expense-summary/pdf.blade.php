<!DOCTYPE html>
<html>
<head>
    <title>Income Expense Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* CSS optimized for PDF rendering */
        body {
            font-family: DejaVu Sans, sans-serif; /* Recommended for supporting Unicode (like ₹) */
            margin: 0;
            padding: 0;
            font-size: 10pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16pt;
            color: #1f2937;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
            text-transform: uppercase;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .filter-info p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .table-container {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e5e7eb;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .income-header { background-color: #d1fae5; color: #065f46; padding: 5px; font-weight: bold; }
        .expense-header { background-color: #fee2e2; color: #991b1b; padding: 5px; font-weight: bold; }
        .text-right { text-align: right; }
        .text-income { color: #065f46; }
        .text-expense { color: #991b1b; }

        /* Summary Footer */
        .summary {
            border-top: 2px solid #1f2937;
            padding-top: 10px;
        }
        .summary div {
            padding: 5px 0;
        }
        .summary strong {
            display: inline-block;
            width: 150px;
        }
        .net-balance {
            font-size: 14pt;
            color: #2563eb;
            font-weight: bold;
            margin-top: 10px;
            text-align: right;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Income & Expense Summary Report</h1>
    </div>

    <div class="filter-info">
        <p><strong>Report Type:</strong> {{ ucwords($type) }}</p>
        <p><strong>Date Range:</strong>
            @if ($startDate && $endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}
            @elseif ($startDate)
                From {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}
            @elseif ($endDate)
                Up to {{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}
            @else
                All Time
            @endif
        </p>
        <p><strong>Generated On:</strong> {{ \Carbon\Carbon::now('Asia/Kolkata')->format('d M, Y H:i A') }}</p>
    </div>

    @if ($type === 'all' || $type === 'income')
        <div class="table-container">
            <div class="income-header">Income Details</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">S.No</th>
                        <th style="width: 15%">Date</th>
                        <th style="width: 40%">Type</th>
                        <th style="width: 25%">Receipt Mode</th>
                        <th style="width: 15%" class="text-right">Amount ({{ $currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $index => $income)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($income->date_time, 'Asia/Kolkata')->format('Y-m-d') }}</td>
                            <td>{{ $income->incomeType->name }}</td>
                            <td>{{ $income->receipt_mode ?? 'N/A' }}</td>
                            <td class="text-right text-income">{{ number_format($income->receipt_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No Income records found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if ($type === 'all' || $type === 'expense')
        <div class="table-container">
            <div class="expense-header">Expense Details</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">S.No</th>
                        <th style="width: 15%">Date</th>
                        <th style="width: 40%">Type</th>
                        <th style="width: 25%">Payment Mode</th>
                        <th style="width: 15%" class="text-right">Amount ({{ $currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($expense->date_time, 'Asia/Kolkata')->format('Y-m-d') }}</td>
                            <td>{{ $expense->expenseType->name }}</td>
                            <td>{{ $expense->payment_mode ?? 'N/A' }}</td>
                            <td class="text-right text-expense">{{ number_format($expense->payment_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">No Expense records found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <div class="summary">
        @if ($type === 'all' || $type === 'income')
        <div>
            <strong>Total Income:</strong>
            <span class="text-income">{{ $currency }}{{ $totalIncome }}</span>
        </div>
        @endif

        @if ($type === 'all' || $type === 'expense')
        <div>
            <strong>Total Expense:</strong>
            <span class="text-expense">{{ $currency }}{{ $totalExpense }}</span>
        </div>
        @endif

        <div>
            <strong>Current Balance:</strong>
            <span>{{ $currency }}{{ $balance }}</span>
        </div>

        <div>
            <strong>Opening Balance:</strong>
            <span>{{ $currency }}{{ $openingBalance }}</span>
        </div>

        <div class="net-balance">
            <strong style="font-size: 12pt;">Net Balance:</strong>
            <span>{{ $currency }}{{ $netBalance }}</span>
        </div>
    </div>

</body>
</html>
