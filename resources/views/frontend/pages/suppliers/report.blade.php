<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppliers</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .hover-scale {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-scale:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .table-row-hover:hover {
            background-color: #f8fafc;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Supplier Management</h1>
            <p class="text-gray-500 mt-2">Manage your suppliers and transactions</p>
        </div>

        @if(auth()->user()->isEmployee())
            <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center mt-4 md:mt-0 px-4 py-2.5 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200 text-gray-700 font-medium">
                <i class="fas fa-chevron-left mr-2 text-sm"></i> Back to Dashboard
            </a>
        @else
            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center mt-4 md:mt-0 px-4 py-2.5 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200 text-gray-700 font-medium">
                <i class="fas fa-chevron-left mr-2 text-sm"></i> Back to Dashboard
            </a>
        @endif
    </div>

    <!-- Filter Card -->
    <div class="glass-card rounded-xl p-6 mb-8 shadow-sm hover-scale">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter mr-2 text-blue-500"></i> Filter Suppliers
        </h3>
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="searchName" class="block text-sm font-medium text-gray-700 mb-1">Search by Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchName" class="pl-10 w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-all duration-200" placeholder="Supplier name...">
                </div>
            </div>
            <div class="flex items-end space-x-3">
                <button type="button" id="filterBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
                <button type="button" id="clearFilterBtn" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-all duration-200 flex items-center">
                    <i class="fas fa-undo mr-2"></i> Reset
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Supplier List Column -->
        <div class="lg:col-span-4">
            <div class="glass-card rounded-xl shadow-sm overflow-hidden hover-scale">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-5">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Supplier Directory</h3>

                    </div>
                </div>
                <div class="h-[500px] overflow-y-auto scrollbar-thin p-1">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-white z-10">
                        <tr class="text-left text-gray-500 border-b border-gray-100">
                            <th class="pb-3 pt-4 px-4 font-medium">Supplier</th>
                        </tr>
                        </thead>
                        <tbody id="supplierTableBody" class="divide-y divide-gray-100">
                        @foreach($suppliers as $supplier)
                            <tr class="supplier-row table-row-hover cursor-pointer transition-colors duration-150"
                                data-supplier-id="{{ $supplier->id }}"
                                data-name="{{ strtolower($supplier->name) }}">
                                <td class="px-4 py-4">
                                    <div class="font-medium text-gray-800">{{ $supplier->name }}</div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div id="noResults" class="hidden p-8 text-center">
                        <div class="mx-auto w-24 h-24 text-gray-300 mb-4">
                            <i class="fas fa-user-slash text-5xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-500">No suppliers found</h4>
                        <p class="text-gray-400 mt-1">Try adjusting your search filters</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Details Column -->
        <div class="lg:col-span-8">
            <div class="glass-card rounded-xl shadow-sm overflow-hidden hover-scale fade-in hidden" id="supplierDetailsCard">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-5">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold" id="supplierName">Supplier Details</h3>
                    </div>
                </div>

                <div class="p-5">
                    <!-- Supplier Info Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm font-medium mb-1">Contact</div>
                            <div class="text-gray-800 font-medium" id="supplierContact">-</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm font-medium mb-1">Address</div>
                            <div class="text-gray-800 font-medium" id="supplierAddress">-</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-500 text-sm font-medium mb-1">Opening Balance</div>
                            <div class="text-gray-800 font-medium" id="supplierBalance">-</div>
                        </div>
                    </div>

                    <!-- Transaction Section Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2 md:mb-0">
                            <i class="fas fa-exchange-alt mr-2 text-blue-500"></i> Transaction History
                        </h4>

                        <!-- Date Filter -->
                        <form id="dateFilterForm" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                            <div class="flex-1 min-w-[150px]">
                                <label for="fromDate" class="sr-only">From Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-day text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="date" id="fromDate" class="pl-10 w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                            <div class="flex-1 min-w-[150px]">
                                <label for="toDate" class="sr-only">To Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-day text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="date" id="toDate" class="pl-10 w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 text-sm">
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" id="filterDateBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 flex items-center">
                                    <i class="fas fa-filter mr-2"></i> Filter
                                </button>
                                <button type="button" id="clearDateFilterBtn" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-all duration-200 flex items-center">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Transactions Table -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="h-[350px] overflow-y-auto scrollbar-thin">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 sticky top-0">
                                <tr class="text-left text-gray-500 border-b border-gray-100">
                                    <th class="py-3 px-4 font-medium">#</th>
                                    <th class="py-3 px-4 font-medium">Date</th>
                                    <th class="py-3 px-4 font-medium">Bill No</th>
                                    <th class="py-3 px-4 font-medium">Type</th>
                                    <th class="py-3 px-4 font-medium text-right">Debit</th>
                                    <th class="py-3 px-4 font-medium text-right">Credit</th>
                                    <th class="py-3 px-4 font-medium">Notes</th>
                                </tr>
                                </thead>
                                <tbody id="transactionTableBody" class="divide-y divide-gray-100">
                                <!-- Transactions will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Transaction Summary -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-red-600 text-sm font-medium mb-1">Total Debit</div>
                            <div class="text-2xl font-semibold text-red-800" id="totalDebit">0.00</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-green-600 text-sm font-medium mb-1">Total Credit</div>
                            <div class="text-2xl font-semibold text-green-800" id="totalCredit">0.00</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-gray-600 text-sm font-medium mb-1">Balance</div>
                            <div class="text-2xl font-semibold" id="totalBalance">0.00</div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6" id="pagination"></div>
                </div>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="glass-card rounded-xl shadow-sm p-8 text-center hover-scale">
                <div class="mx-auto w-20 h-20 text-gray-300 mb-4">
                    <i class="fas fa-user-tie text-6xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-500">Select a supplier</h4>
                <p class="text-gray-400 mt-2">Click on a supplier from the list to view details and transactions</p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        // Cache DOM elements
        const $searchName = $('#searchName');
        const $filterBtn = $('#filterBtn');
        const $clearFilterBtn = $('#clearFilterBtn');
        const $supplierTableBody = $('#supplierTableBody');
        const $noResults = $('#noResults');
        const $fromDate = $('#fromDate');
        const $toDate = $('#toDate');
        const $filterDateBtn = $('#filterDateBtn');
        const $clearDateFilterBtn = $('#clearDateFilterBtn');
        const $supplierDetailsCard = $('#supplierDetailsCard');
        const $emptyState = $('#emptyState');

        // Supplier filter function
        function applySupplierFilters() {
            const searchText = $searchName.val().toLowerCase().trim();
            let hasVisibleRows = false;

            $('.supplier-row').each(function () {
                const $row = $(this);
                const name = $row.data('name');

                const matchesName = !searchText || name.includes(searchText);

                if (matchesName) {
                    $row.show();
                    hasVisibleRows = true;
                } else {
                    $row.hide();
                }
            });

            $noResults.toggleClass('hidden', hasVisibleRows);
            $supplierTableBody.toggleClass('hidden', !hasVisibleRows);
        }

        // Validate date range
        function validateDateRange() {
            const fromDate = $fromDate.val();
            const toDate = $toDate.val();
            let isValid = true;

            $('.invalid-feedback').addClass('hidden').text('');
            $fromDate.removeClass('border-red-500');
            $toDate.removeClass('border-red-500');

            if (fromDate && toDate && fromDate > toDate) {
                $fromDate.addClass('border-red-500');
                $toDate.addClass('border-red-500');
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date Range',
                    text: 'From date cannot be after To date',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                isValid = false;
            }

            $filterDateBtn.prop('disabled', !isValid);
            return isValid;
        }

        // Fetch transactions for selected supplier
        function fetchTransactions(supplierId, page) {
            const fromDate = $fromDate.val();
            const toDate = $toDate.val();
            let url = `/suppliers/${supplierId}/transactions?page=${page}`;
            if (fromDate || toDate) {
                url += `&from_date=${fromDate}&to_date=${toDate}`;
            }

            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    // Show loading state
                    $('#transactionTableBody').html(`
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                <i class="fas fa-circle-notch fa-spin text-blue-500 text-2xl mb-2"></i>
                                <p>Loading transactions...</p>
                            </td>
                        </tr>
                    `);
                },
                success: function (response) {
                    if (response.success) {
                        // Update supplier details
                        $('#supplierName').text(response.supplier.name || 'Unknown Supplier');
                        $('#supplierContact').text(response.supplier.contact_number || 'Not provided');
                        $('#supplierAddress').text(response.supplier.address || 'Not provided');
                        $('#supplierBalance').text(parseFloat(response.supplier.opening_balance || 0).toFixed(2));

                        // Update transactions table
                        const tbody = $('#transactionTableBody');
                        tbody.empty();

                        if (response.transactions.length === 0) {
                            tbody.html(`
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-exchange-alt text-gray-300 text-2xl mb-2"></i>
                                        <p>No transactions found</p>
                                    </td>
                                </tr>
                            `);
                        } else {
                            response.transactions.forEach((transaction, index) => {
                                const date = transaction.date ? new Date(transaction.date).toLocaleDateString('en-US', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                }) : '-';

                                const row = `
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-gray-500">${index + 1 + (response.pagination.per_page * (response.pagination.current_page - 1))}</td>
                                        <td class="px-4 py-3 font-medium">${date}</td>
                                        <td class="px-4 py-3">${transaction.bill_number || '-'}</td>
                                        <td class="px-4 py-3">${transaction.transaction_type || '-'}</td>
                                        <td class="px-4 py-3 text-right text-red-600 font-medium">${parseFloat(transaction.debit || 0).toFixed(2)}</td>
                                        <td class="px-4 py-3 text-right text-green-600 font-medium">${parseFloat(transaction.credit || 0).toFixed(2)}</td>
                                        <td class="px-4 py-3 text-gray-500">${transaction.notes || '-'}</td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                        }

                        // Update totals
                        const totals = response.totals || { total_debit: 0, total_credit: 0, balance: 0 };
                        const totalDebit = parseFloat(totals.total_debit || 0);
                        const totalCredit = parseFloat(totals.total_credit || 0);
                        const balance = parseFloat(totals.balance || 0);

                        $('#totalDebit').text(totalDebit.toFixed(2));
                        $('#totalCredit').text(totalCredit.toFixed(2));

                        const $balanceElement = $('#totalBalance');
                        $balanceElement.text(Math.abs(balance).toFixed(2));
                        $balanceElement.removeClass('text-green-600 text-red-600');

                        if (balance >= 0) {
                            $balanceElement.addClass('text-green-600');
                        } else {
                            $balanceElement.addClass('text-red-600');
                        }

                        // Update pagination
                        const pagination = $('#pagination');
                        pagination.empty();
                        if (response.pagination.last_page > 1) {
                            let paginationHtml = `
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-500">
                                        Showing ${response.pagination.from} to ${response.pagination.to} of ${response.pagination.total} entries
                                    </div>
                                    <div class="flex space-x-1">
                                        <button ${response.pagination.current_page === 1 ? 'disabled' : ''}
                                            class="px-3 py-1 border rounded-md ${response.pagination.current_page === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}"
                                            data-page="${response.pagination.current_page - 1}">
                                            Previous
                                        </button>
                            `;

                            // Always show first page
                            if (response.pagination.current_page > 2) {
                                paginationHtml += `
                                    <button class="px-3 py-1 border rounded-md bg-white text-gray-700 hover:bg-gray-50" data-page="1">1</button>
                                    ${response.pagination.current_page > 3 ? '<span class="px-3 py-1">...</span>' : ''}
                                `;
                            }

                            // Show pages around current page
                            const startPage = Math.max(1, response.pagination.current_page - 1);
                            const endPage = Math.min(response.pagination.last_page, response.pagination.current_page + 1);

                            for (let i = startPage; i <= endPage; i++) {
                                paginationHtml += `
                                    <button class="px-3 py-1 border rounded-md ${response.pagination.current_page === i ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}"
                                        data-page="${i}">
                                        ${i}
                                    </button>
                                `;
                            }

                            // Always show last page
                            if (response.pagination.current_page < response.pagination.last_page - 1) {
                                paginationHtml += `
                                    ${response.pagination.current_page < response.pagination.last_page - 2 ? '<span class="px-3 py-1">...</span>' : ''}
                                    <button class="px-3 py-1 border rounded-md bg-white text-gray-700 hover:bg-gray-50" data-page="${response.pagination.last_page}">
                                        ${response.pagination.last_page}
                                    </button>
                                `;
                            }

                            paginationHtml += `
                                        <button ${response.pagination.current_page === response.pagination.last_page ? 'disabled' : ''}
                                            class="px-3 py-1 border rounded-md ${response.pagination.current_page === response.pagination.last_page ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}"
                                            data-page="${response.pagination.current_page + 1}">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            `;
                            pagination.html(paginationHtml);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch supplier transactions.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to fetch supplier transactions.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        }

        // Handle supplier click
        $(document).on('click', '.supplier-row', function () {
            const supplierId = $(this).data('supplier-id');
            $('.supplier-row').removeClass('bg-blue-50');
            $(this).addClass('bg-blue-50');
            $supplierDetailsCard.removeClass('hidden').addClass('fade-in');
            $emptyState.addClass('hidden');
            $('#dateFilterForm')[0].reset();
            $fromDate.removeClass('border-red-500');
            $toDate.removeClass('border-red-500');
            $filterDateBtn.prop('disabled', false);
            fetchTransactions(supplierId, 1);
        });

        // Handle pagination click
        $(document).on('click', '[data-page]', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            const supplierId = $('.supplier-row.bg-blue-50').data('supplier-id');
            if (!supplierId || !page) return;
            fetchTransactions(supplierId, page);
        });

        // Handle date filter form submission
        $('#dateFilterForm').on('submit', function (e) {
            e.preventDefault();
            if (!validateDateRange()) return;

            const supplierId = $('.supplier-row.bg-blue-50').data('supplier-id');
            if (!supplierId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Supplier Selected',
                    text: 'Please select a supplier first.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }
            fetchTransactions(supplierId, 1);
        });

        // Handle clear date filter button
        $clearDateFilterBtn.on('click', function () {
            $('#dateFilterForm')[0].reset();
            $fromDate.removeClass('border-red-500');
            $toDate.removeClass('border-red-500');
            $filterDateBtn.prop('disabled', false);

            const supplierId = $('.supplier-row.bg-blue-50').data('supplier-id');
            if (!supplierId) return;
            fetchTransactions(supplierId, 1);
        });

        // Handle supplier filter button click
        $filterBtn.on('click', applySupplierFilters);

        // Handle clear supplier filter button click
        $clearFilterBtn.on('click', function () {
            $searchName.val('');
            applySupplierFilters();
        });

        // Handle enter key press in search input
        $searchName.on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
                applySupplierFilters();
            }
        });

        // Handle date input changes for validation
        $fromDate.add($toDate).on('change', validateDateRange);

        // Initialize with empty state
        $supplierDetailsCard.addClass('hidden');
        $emptyState.removeClass('hidden');

        // Set default dates for date filter (current month)
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        $fromDate.val(firstDay.toISOString().split('T')[0]);
        $toDate.val(lastDay.toISOString().split('T')[0]);

        // Apply initial supplier filters
        applySupplierFilters();
    });
</script>
</body>
</html>
