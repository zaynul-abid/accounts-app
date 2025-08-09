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
        input::placeholder {
            color: #9ca3af;
            font-style: italic;
        }
        .income-only .expense-table-container {
            display: none;
        }
        .expense-only .income-table-container {
            display: none;
        }
        .filter-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            gap: 1rem;
        }
        .filter-controls {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }
        .date-inputs {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }
        .buttons {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.25rem;
            padding: 0.75rem;
        }
        .pagination a, .pagination span {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: all 0.2s;
            background-color: #f3f4f6;
        }
        .pagination a:hover {
            background-color: #e5e7eb;
        }
        .pagination .active {
            background-color: #2563eb;
            color: white;
        }
        .pagination .disabled {
            color: #9ca3af;
            pointer-events: none;
            background-color: #f3f4f6;
        }
        .pagination .page-count {
            font-size: 0.875rem;
            color: #374151;
            padding: 0.25rem 0.75rem;
        }
        @media (max-width: 768px) {
            .filter-container {
                flex-wrap: nowrap;
                gap: 0.75rem;
            }
            .filter-controls {
                gap: 0.4rem;
            }
            .date-inputs {
                gap: 0.4rem;
            }
            .buttons {
                gap: 0.4rem;
            }
            .filter-controls input,
            .filter-controls button,
            .filter-controls a {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .filter-controls input[type="date"] {
                width: 120px;
            }
            .pagination a, .pagination span, .pagination .page-count {
                padding: 0.2rem 0.6rem;
                font-size: 0.75rem;
            }
        }
        @media (max-width: 576px) {
            .filter-container {
                gap: 0.5rem;
            }
            .filter-controls {
                gap: 0.3rem;
            }
            .date-inputs {
                gap: 0.3rem;
            }
            .buttons {
                gap: 0.3rem;
            }
            .filter-controls input,
            .filter-controls button,
            .filter-controls a {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }
            .filter-controls input[type="date"] {
                width: 100px;
            }
            .pagination a, .pagination span, .pagination .page-count {
                padding: 0.15rem 0.5rem;
                font-size: 0.65rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">
<!-- Header -->
<div class="text-black text-center py-4 shadow-md bg-white">
    <h1 class="text-xl md:text-2xl font-bold uppercase tracking-wide">
        {{ request()->get('report') === 'income' ? 'Income Report' : (request()->get('report') === 'expense' ? 'Expense Report' : 'Income Expense Summary Report') }}
    </h1>
</div>

<!-- Filter Section -->
<div class="bg-white shadow-md rounded-md p-4 md:p-6 mt-4 max-w-6xl mx-auto">
    <div class="filter-container">
        <!-- Filter Options -->
        <div class="flex flex-wrap gap-4">
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="all" class="form-radio type-filter"
                    {{ !request()->has('report') ? 'checked' : '' }}
                    {{ request()->get('report') === 'income' || request()->get('report') === 'expense' ? 'disabled' : '' }}>
                <span class="ml-2 text-sm md:text-base">All</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="income" class="form-radio type-filter"
                    {{ request()->get('report') === 'income' ? 'checked' : '' }}
                    {{ request()->get('report') === 'expense' ? 'disabled' : '' }}>
                <span class="ml-2 text-sm md:text-base">Income</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="type" value="expense" class="form-radio type-filter"
                    {{ request()->get('report') === 'expense' ? 'checked' : '' }}
                    {{ request()->get('report') === 'income' ? 'disabled' : '' }}>
                <span class="ml-2 text-sm md:text-base">Expense</span>
            </label>
        </div>

        <!-- Date Filters & Buttons -->
        <div class="filter-controls">
            <div class="date-inputs">
                <button id="toggle-date-filter" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
                    Show Today
                </button>
                <input type="date" id="start-date" placeholder="Start Date"
                       class="border rounded px-3 py-2 text-sm placeholder-gray-400">
                <input type="date" id="end-date" placeholder="End Date"
                       class="border rounded px-3 py-2 text-sm placeholder-gray-400">
            </div>
            <div class="buttons">
                <button id="apply-filter"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm">
                    Apply Filter
                </button>
                <button id="clear-dates"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded hover:bg-gray-200 transition text-sm">
                    Clear
                </button>
                @if(auth()->user()->isEmployee())
                    <a href="{{ route('employee.dashboard') }}"
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition text-sm text-center">
                        Exit
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}"
                       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition text-sm text-center">
                        Exit
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="max-w-6xl mx-auto mt-4 grid grid-cols-1 md:grid-cols-2 gap-6" id="table-container">
    <!-- Income Table -->
    <div class="bg-white shadow-md rounded-md overflow-hidden income-table-container">
        <div class="bg-green-100 px-4 py-2 font-semibold text-green-800">Income</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">S.No</th>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="income" data-column="date">Date</button>
                    </th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="income" data-column="payment_mode">Receipt Mode</button>
                    </th>
                    <th class="px-4 py-3 text-right">
                        <button class="sort-btn" data-type="income" data-column="amount">Amount</button>
                    </th>
                </tr>
                </thead>
                <tbody id="income-table" class="divide-y divide-gray-100">
                @foreach($incomes as $index => $income)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $incomes->firstItem() + $index }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($income->date_time)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $income->incomeType->name }}</td>
                        <td class="px-4 py-3">{{ $income->receipt_mode ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ $income->receipt_amount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination" id="income-pagination">
                {{ $incomes->links('pagination::custom-simple') }}
                <span class="page-count">Page {{ $incomes->currentPage() }} of {{ $incomes->lastPage() }}</span>
            </div>
        </div>
    </div>

    <!-- Expense Table -->
    <div class="bg-white shadow-md rounded-md overflow-hidden expense-table-container">
        <div class="bg-red-100 px-4 py-2 font-semibold text-red-800">Expense</div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">S.No</th>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="expense" data-column="date">Date</button>
                    </th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">
                        <button class="sort-btn" data-type="expense" data-column="payment_mode">Payment Mode</button>
                    </th>
                    <th class="px-4 py-3 text-right">
                        <button class="sort-btn" data-type="expense" data-column="amount">Amount</button>
                    </th>
                </tr>
                </thead>
                <tbody id="expense-table" class="divide-y divide-gray-100">
                @foreach($expenses as $index => $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $expenses->firstItem() + $index }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($expense->date_time)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $expense->expenseType->name }}</td>
                        <td class="px-4 py-3">{{ $expense->payment_mode ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ $expense->payment_amount }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination" id="expense-pagination">
                {{ $expenses->links('pagination::custom-simple') }}
                <span class="page-count">Page {{ $expenses->currentPage() }} of {{ $expenses->lastPage() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Summary Footer -->
<div class="max-w-6xl mx-auto mt-4 mb-10 bg-white shadow-md rounded-md p-4 md:p-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
    <div>
        <p id="total-income-row" class="{{ request()->get('report') === 'expense' ? 'hidden' : '' }}">
            <strong>Total Income:</strong> <span id="total-income" class="text-green-600">₹0</span>
        </p>
        <p id="total-expense-row" class="{{ request()->get('report') === 'income' ? 'hidden' : '' }}">
            <strong>Total Expense:</strong> <span id="total-expense" class="text-red-600">₹0</span>
        </p>
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
        let isTodayFilter = true;

        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        function setCurrentDate() {
            const today = new Date().toISOString().split('T')[0];
            $('#start-date').val(today);
            $('#end-date').val(today);
        }

        function updateToggleButtonText() {
            $('#toggle-date-filter').text(isTodayFilter ? 'Show All' : 'Show Today');
        }

        const reportType = getQueryParam('report');
        if (reportType === 'income') {
            $('input[name="type"][value="income"]').prop('checked', true);
            $('#table-container').addClass('income-only');
            $('#total-expense-row').hide();
            $('#total-income-row').show();
            $('input[name="type"][value="expense"]').prop('disabled', true);
            $('input[name="type"][value="all"]').prop('disabled', true);
        } else if (reportType === 'expense') {
            $('input[name="type"][value="expense"]').prop('checked', true);
            $('#table-container').addClass('expense-only');
            $('#total-income-row').hide();
            $('#total-expense-row').show();
            $('input[name="type"][value="income"]').prop('disabled', true);
            $('input[name="type"][value="all"]').prop('disabled', true);
        } else {
            $('input[name="type"][value="all"]').prop('checked', true);
            $('#table-container').removeClass('income-only expense-only');
            $('#total-income-row').show();
            $('#total-expense-row').show();
            $('input[name="type"]').prop('disabled', false);
        }

        setCurrentDate();
        updateToggleButtonText();

        function fetchData(applyDateFilter = true, incomePage = 1, expensePage = 1) {
            const type = $('input[name="type"]:checked').val();
            const data = {
                type: type,
                income_sort_column: sortConfig.income.column,
                income_sort_order: sortConfig.income.order,
                expense_sort_column: sortConfig.expense.column,
                expense_sort_order: sortConfig.expense.order,
                income_page: incomePage,
                expense_page: expensePage
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
                    $('#income-pagination').html(response.income_pagination);
                    $('#expense-pagination').html(response.expense_pagination);
                    $('#total-income').text('₹' + response.total_income);
                    $('#total-expense').text('₹' + response.total_expense);
                    $('#balance').text('₹' + response.balance);
                    $('#opening-balance').text('₹' + response.opening_balance);
                    $('#net-balance').text('₹' + response.net_balance);

                    if (type === 'income') {
                        $('#table-container').addClass('income-only').removeClass('expense-only');
                        $('#total-expense-row').hide();
                        $('#total-income-row').show();
                    } else if (type === 'expense') {
                        $('#table-container').addClass('expense-only').removeClass('income-only');
                        $('#total-income-row').hide();
                        $('#total-expense-row').show();
                    } else {
                        $('#table-container').removeClass('income-only expense-only');
                        $('#total-income-row').show();
                        $('#total-expense-row').show();
                    }

                    if (reportType === 'income') {
                        $('input[name="type"][value="expense"]').prop('disabled', true);
                        $('input[name="type"][value="all"]').prop('disabled', true);
                    } else if (reportType === 'expense') {
                        $('input[name="type"][value="income"]').prop('disabled', true);
                        $('input[name="type"][value="all"]').prop('disabled', true);
                    } else {
                        $('input[name="type"]').prop('disabled', false);
                    }

                    bindPaginationEvents();
                }
            });
        }

        function bindPaginationEvents() {
            $('#income-pagination a, #expense-pagination a').on('click', function (e) {
                e.preventDefault();
                const url = new URL($(this).attr('href'));
                const incomePage = url.searchParams.get('income_page') || 1;
                const expensePage = url.searchParams.get('expense_page') || 1;
                fetchData(isTodayFilter, incomePage, expensePage);
            });
        }

        $('#apply-filter').on('click', function () {
            isTodayFilter = false;
            updateToggleButtonText();
            fetchData(true);
        });

        $('#clear-dates').on('click', function () {
            $('#start-date').val('');
            $('#end-date').val('');
            isTodayFilter = false;
            updateToggleButtonText();
            fetchData(false);
        });

        $('#toggle-date-filter').on('click', function () {
            if (isTodayFilter) {
                $('#start-date').val('');
                $('#end-date').val('');
                isTodayFilter = false;
            } else {
                setCurrentDate();
                isTodayFilter = true;
            }
            updateToggleButtonText();
            fetchData(isTodayFilter);
        });

        $('.type-filter').on('change', function () {
            const type = $(this).val();
            let newUrl = '/income-expense-summary';
            if (type === 'income') {
                newUrl += '?report=income';
            } else if (type === 'expense') {
                newUrl += '?report=expense';
            }
            history.pushState({}, '', newUrl);
            fetchData(isTodayFilter);
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

            fetchData(isTodayFilter);
        });

        fetchData(true);
    });
</script>
</body>
</html>