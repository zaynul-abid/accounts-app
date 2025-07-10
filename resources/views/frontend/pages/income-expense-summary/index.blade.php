<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Expense Summary</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/income-expense.js') }}"></script>
    <style>
        @media (max-width: 768px) {
            .filter-container {
                flex-direction: column;
                gap: 1rem;
            }

            .filter-controls {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
            }

            .filter-controls .buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

<!-- Header -->
<div class="text-black text-center py-4 shadow-md bg-white">
    <h1 class="text-xl md:text-2xl font-bold uppercase tracking-wide">Income Expense Summary Report</h1>
</div>

<!-- Filter Section -->
<div class="bg-white shadow-md rounded-md p-4 md:p-6 mt-4 max-w-6xl mx-auto">
    <div class="flex filter-container items-start justify-between gap-4 flex-wrap md:flex-nowrap">
        <!-- Filter Options -->
        <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="all" class="form-radio type-filter" checked>
                <span class="ml-2 text-sm md:text-base">All</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="income" class="form-radio type-filter">
                <span class="ml-2 text-sm md:text-base">Income</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="expense" class="form-radio type-filter">
                <span class="ml-2 text-sm md:text-base">Expense</span>
            </label>
        </div>

        <!-- Date Filters & Buttons -->
        <div class="flex filter-controls flex-wrap md:flex-nowrap md:items-center md:justify-end w-full md:w-auto gap-4">
            <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
                <input type="date" id="start-date" placeholder="Start Date"
                       class="border rounded px-3 py-2 w-full text-sm placeholder-gray-400">
                <input type="date" id="end-date" placeholder="End Date"
                       class="border rounded px-3 py-2 w-full text-sm placeholder-gray-400">
            </div>
            <div class="flex buttons gap-2">
                <button id="apply-filter"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm w-full md:w-auto">
                    Apply Filter
                </button>
                <button id="clear-dates"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded hover:bg-gray-200 transition text-sm w-full md:w-auto">
                    Clear
                </button>
                @if(auth()->user()->isEmployee())
                    <a href="{{route('employee.dashboard')}}"
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition text-sm w-full md:w-auto text-center">
                        Exit
                    </a>
                @else
                    <a href="{{route('admin.dashboard')}}"
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition text-sm w-full md:w-auto text-center">
                        Exit
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="max-w-6xl mx-auto mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Income Table -->
    <div class="bg-white shadow-md rounded-md overflow-hidden">
        <div class="bg-green-100 px-4 py-2 font-semibold text-green-800">Income</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase">
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
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase">
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
<div class="max-w-6xl mx-auto mt-4 mb-10 bg-white shadow-md rounded-md p-4 md:p-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
    <div>
        <p><strong>Total Income:</strong> <span id="total-income" class="text-green-600">₹0</span></p>
        <p><strong>Total Expense:</strong> <span id="total-expense" class="text-red-600">₹0</span></p>
        <p><strong>Balance:</strong> <span id="balance">₹0</span></p>
    </div>
    <div>
        <p><strong>Opening Balance:</strong> <span id="opening-balance">₹0</span></p>
    </div>
    <div>
        <p><strong>Net Balance:</strong> <span id="net-balance" class="text-blue-600 font-semibold text-lg">₹0</span></p>
    </div>
</div>

<!-- Scripts -->
<script>
    $(document).ready(function () {
        let sortConfig = {
            income: { column: 'date', order: 'desc' },
            expense: { column: 'date', order: 'desc' }
        };

        function fetchData(applyDateFilter = false) {
            const type = $('input[name="type"]:checked').val();
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
                success: function (response) {
                    $('#income-table').html(response.incomes);
                    $('#expense-table').html(response.expenses);
                    $('#total-income').text('₹' + response.total_income);
                    $('#total-expense').text('₹' + response.total_expense);
                    $('#balance').text('₹' + response.balance);
                    $('#opening-balance').text('₹' + response.opening_balance);
                    $('#net-balance').text('₹' + response.net_balance);
                }
            });
        }

        $('#apply-filter').on('click', function () {
            fetchData(true);
        });

        $('#clear-dates').on('click', function () {
            $('#start-date').val('');
            $('#end-date').val('');
            fetchData(false);
        });

        $('.type-filter').on('change', function () {
            fetchData(false);
        });

        $('.sort-btn').on('click', function () {
            const type = $(this).data('type');
            const column = $(this).data('column');

            if (sortConfig[type].column === column) {
                sortConfig[type].order = sortConfig[type].order === 'desc' ? 'asc' : 'desc';
            } else {
                sortConfig[type].column = column;
                sortConfig[type].order = 'desc';
            }

            fetchData(true);
        });

        fetchData(false);
    });
</script>
</body>
</html>

