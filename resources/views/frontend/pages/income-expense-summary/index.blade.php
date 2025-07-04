<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Income Expense Summary</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/income-expense.js') }}"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
<!-- Header -->
<div class="text-black text-center py-5 shadow-md">
    <h1 class="text-2xl font-bold uppercase tracking-wide">Income Expense Summary Report</h1>
</div>

<!-- Filter Section -->
<div class="bg-white shadow-md rounded-md p-6 mt-6 max-w-6xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="flex items-center gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="type" value="all" class="form-radio type-filter" checked>
            <span class="ml-2">All</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="type" value="income" class="form-radio type-filter">
            <span class="ml-2">Income</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="type" value="expense" class="form-radio type-filter">
            <span class="ml-2">Expense</span>
        </label>
    </div>
    <div class="flex items-center gap-2">
        <input type="date" id="start-date" class="border rounded px-3 py-2" placeholder="Select start date">
        <span>to</span>
        <input type="date" id="end-date" class="border rounded px-3 py-2" placeholder="Select end date">
        <button id="apply-filter" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Apply Filter</button>
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">Print</button>
        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">Exit</button>
    </div>
</div>
<!-- Dual Tables -->
<div class="max-w-6xl mx-auto mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Income Table -->
    <div class="bg-white shadow-md rounded-md overflow-hidden">
        <div class="bg-green-100 px-4 py-2 font-semibold text-green-800">Income</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="income" data-column="date">Date</button>
                    </th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-right">
                        <button class="sort-btn" data-type="income" data-column="amount">Amount</button>
                    </th>
                </tr>
                </thead>
                <tbody id="income-table" class="divide-y divide-gray-100">
                @foreach($incomes as $income)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($income->date_time)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $income->incomeType->name }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ $income->receipt_amount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expense Table -->
    <div class="bg-white shadow-md rounded-md overflow-hidden">
        <div class="bg-red-100 px-4 py-2 font-semibold text-red-800">Expense</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="expense" data-column="date">Date</button>
                    </th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-right">
                        <button class="sort-btn" data-type="expense" data-column="amount">Amount</button>
                    </th>
                </tr>
                </thead>
                <tbody id="expense-table" class="divide-y divide-gray-100">
                @foreach($expenses as $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($expense->date_time)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $expense->expenseType->name }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ $expense->payment_amount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Summary Footer -->
<div class="max-w-6xl mx-auto mt-6 mb-10 bg-white shadow-md rounded-md p-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
    <div class="space-y-1">
        <p><strong>Total Income:</strong> <span id="total-income" class="text-green-600">₹0</span></p>
        <p><strong>Total Expense:</strong> <span id="total-expense" class="text-red-600">₹0</span></p>
        <p><strong>Balance:</strong> <span id="balance">₹0</span></p>
    </div>
    <div class="space-y-1">
        <p><strong>Opening Balance:</strong> <span id="opening-balance">₹0</span></p>
    </div>
    <div class="space-y-1">
        <p><strong>Net Balance:</strong> <span id="net-balance" class="text-blue-600 font-semibold text-lg">₹0</span></p>
    </div>
</div>
<script>
    $(document).ready(function() {
        let sortConfig = {
            income: { column: 'date', order: 'desc' },
            expense: { column: 'date', order: 'desc' }
        };

        function fetchData(applyDateFilter = false) {
            const type = $('input[name="type"]:checked').val();
            // Only include date parameters if applyDateFilter is true
            const data = {
                type: type,
                income_sort_column: sortConfig.income.column,
                income_sort_order: sortConfig.income.order,
                expense_sort_column: sortConfig.expense.column,
                expense_sort_order: sortConfig.expense.order
            };

            if (applyDateFilter) {
                data.start_date = $('#start-date').val();
                data.end_date = $('#end-date').val();
            }

            $.ajax({
                url: '/income-expense/summary/data',
                method: 'GET',
                data: data,
                success: function(response) {
                    // Update tables
                    $('#income-table').html(response.incomes);
                    $('#expense-table').html(response.expenses);

                    // Update summary
                    $('#total-income').text('₹' + response.total_income);
                    $('#total-expense').text('₹' + response.total_expense);
                    $('#balance').text('₹' + response.balance);
                    $('#opening-balance').text('₹' + response.opening_balance);
                    $('#net-balance').text('₹' + response.net_balance);
                }
            });
        }

        // Radio button filter
        $('.type-filter').on('change', function() {
            fetchData(false); // Don't apply date filter on type change
        });

        // Apply date filter only when the Filter button is clicked
        $('#apply-filter').on('click', function() {
            fetchData(true); // Apply date filter
        });

        // Sort buttons
        $('.sort-btn').on('click', function() {
            const type = $(this).data('type');
            const column = $(this).data('column');

            // Toggle sort order
            if (sortConfig[type].column === column) {
                sortConfig[type].order = sortConfig[type].order === 'desc' ? 'asc' : 'desc';
            } else {
                sortConfig[type].column = column;
                sortConfig[type].order = 'desc';
            }

            fetchData($('input[name="type"]:checked').val() !== 'all'); // Maintain date filter if already applied
        });

        // Initial data load (show all data)
        fetchData(false);
    });
</script>
</body>

</html>
