<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        @media (max-width: 768px) {
            .filter-container {
                flex-wrap: nowrap;
                gap: 0.75rem;
            }
            .filter-controls {
                gap: 0.4rem;
            }
            .filter-controls input,
            .filter-controls button {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .filter-controls input[type="date"] {
                width: 120px;
            }
        }
        @media (max-width: 576px) {
            .filter-container {
                gap: 0.5rem;
            }
            .filter-controls {
                gap: 0.3rem;
            }
            .filter-controls input,
            .filter-controls button {
                font-size: 0.65rem;
                padding: 0.2rem 0.4rem;
            }
            .filter-controls input[type="date"] {
                width: 100px;
            }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <!-- Header with back button -->
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.dashboard') }}" class="mr-4 p-2 rounded-full bg-white shadow-sm text-gray-600 hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Transaction Overview</h1>
    </div>

    <!-- Date Filter Card -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-5">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Filter Transactions</h2>
            <form id="filterForm" action="{{ route('transactions.index') }}" method="GET" class="filter-container">
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? \Carbon\Carbon::today('Asia/Kolkata')->format('Y-m-d') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? \Carbon\Carbon::today('Asia/Kolkata')->format('Y-m-d') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2 px-3 border">
                </div>
                <div class="filter-controls sm:self-end flex space-x-2">
                    <button type="button" id="toggle-date-filter"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-sm font-medium">
                        Show All
                    </button>
                    <button type="submit" id="applyFilters"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-sm font-medium flex items-center">
                        <span class="loading-text">Apply</span>
                        <span class="loading-spinner hidden ml-2"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                    <button type="button" id="clearFilters"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors text-sm font-medium">
                        Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Methods Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <!-- All Banks Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="all_banks" data-value="1" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-gray-50 text-gray-500 mr-3">
                        <i class="fas fa-university text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">All Banks</h3>
                        <p class="text-sm text-gray-500">Combined bank accounts</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ $allBanksBalance >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($allBanksBalance ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- Cash Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="mode" data-value="cash" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-50 text-red-500 mr-3">
                        <i class="fas fa-money-bill-wave text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Cash</h3>
                        <p class="text-sm text-gray-500">Physical currency</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ ($cashBalance ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($cashBalance ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- Credit Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="mode" data-value="credit" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-500 mr-3">
                        <i class="fas fa-credit-card text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Credit</h3>
                        <p class="text-sm text-gray-500">Card payments</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ ($creditBalance ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($creditBalance ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- Touch & Go Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="mode" data-value="touch&go" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-50 text-green-500 mr-3">
                        <i class="fas fa-mobile-alt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Touch & Go</h3>
                        <p class="text-sm text-gray-500">Mobile wallet</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ ($modeBalances['touch&go'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($modeBalances['touch&go'] ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- Boost Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="mode" data-value="boost" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50 text-blue-500 mr-3">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">Boost</h3>
                        <p class="text-sm text-gray-500">E-wallet</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ ($modeBalances['boost'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($modeBalances['boost'] ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- DuitNow Card -->
        <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <a href="#" data-type="mode" data-value="duitinow" class="block p-4 transaction-link">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-50 text-indigo-500 mr-3">
                        <i class="fas fa-exchange-alt text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900">DuitNow</h3>
                        <p class="text-sm text-gray-500">QR payments</p>
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <span class="{{ ($modeBalances['duitinow'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ number_format($modeBalances['duitinow'] ?? 0, 2) }}
                    </span>
                </div>
            </a>
        </div>

        <!-- Individual Bank Cards -->
        @foreach ($bankBalances as $bankId => $bank)
            <div class="payment-method bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <a href="#" data-type="bank_id" data-value="{{ $bankId }}" class="block p-4 transaction-link">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-gray-50 text-gray-500 mr-3">
                            <i class="fas fa-university text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900">{{ $bank['name'] }}</h3>
                            <p class="text-sm text-gray-500">Bank account</p>
                        </div>
                    </div>
                    <div class="mt-3 text-right">
                        <span class="{{ ($bank['balance'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                            {{ number_format($bank['balance'] ?? 0, 2) }}
                        </span>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Modal for Transaction Details -->
    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden transition-opacity duration-300"
         role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex justify-between items-center border-b border-gray-200 p-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900"></h3>
                <button class="modal-close-button text-gray-400 hover:text-gray-500" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="overflow-y-auto flex-1 relative" id="transactionsTableContainer">
                <div id="transactionsLoadingOverlay" class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center z-10 hidden">
                    <span class="text-indigo-600"><i class="fas fa-spinner fa-spin fa-2x"></i></span>
                </div>
                <table class="min-w-full divide-y divide-gray-200 transition-opacity duration-200" id="transactionsTable">
                    <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th id="bankColumnHeader" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden">Bank</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                    </tr>
                    </thead>
                    <tbody id="transactionsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Transactions will be loaded here via AJAX -->
                    </tbody>
                </table>
                <div id="transactionsPagination" class="flex justify-center items-center gap-2 py-3"></div>
            </div>
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-700">Total Balance</span>
                    <span id="modalBalance" class="font-semibold"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        console.log('jQuery version:', $.fn.jquery);

        let isTodayFilter = true; // Track toggle state (true = Show Today, false = Show All)

        // Store the current filter state
        let currentFilters = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            selectedType: null,
            selectedValue: null
        };

        // Set current date as default for start and end date
        function setCurrentDate() {
            const today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            $('#end_date').val(today);
            currentFilters.start_date = today;
            currentFilters.end_date = today;
        }

        // Update toggle button text based on state
        function updateToggleButtonText() {
            $('#toggle-date-filter').text(isTodayFilter ? 'Show All' : 'Show Today');
        }

        // Set initial date to today and update button text
        setCurrentDate();
        updateToggleButtonText();

        // Toggle date filter button
        $('#toggle-date-filter').on('click', function() {
            if (isTodayFilter) {
                $('#start_date').val('');
                $('#end_date').val('');
                currentFilters.start_date = '';
                currentFilters.end_date = '';
                isTodayFilter = false;
            } else {
                setCurrentDate();
                isTodayFilter = true;
            }
            updateToggleButtonText();
            $('#filterForm').submit();
        });

        // Apply filters form submission (AJAX)
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const url = $(this).attr('action');
            $('#applyFilters').addClass('loading').find('.loading-text').addClass('hidden').end().find('.loading-spinner').removeClass('hidden');

            $.ajax({
                url: url,
                type: 'GET',
                data: formData,
                success: function(response) {
                    console.log('Filter response:', response);
                    updateBalances(response);
                    currentFilters.start_date = $('#start_date').val();
                    currentFilters.end_date = $('#end_date').val();
                    if (currentFilters.selectedType && currentFilters.selectedValue) {
                        loadTransactions(currentFilters.selectedType, currentFilters.selectedValue);
                    }
                },
                error: function(xhr) {
                    console.error('Filter error:', xhr.responseText);
                    alert('Failed to apply filters. Please try again.');
                },
                complete: function() {
                    $('#applyFilters').removeClass('loading').find('.loading-text').removeClass('hidden').end().find('.loading-spinner').addClass('hidden');
                }
            });
        });

        // Clear filters button
        $('#clearFilters').on('click', function() {
            $('#start_date').val('');
            $('#end_date').val('');
            currentFilters.start_date = '';
            currentFilters.end_date = '';
            isTodayFilter = false;
            updateToggleButtonText();
            $('#filterForm').submit();
        });

        // Transaction link click handler
        $('.transaction-link').on('click', function(e) {
            e.preventDefault();
            const type = $(this).data('type');
            const value = $(this).data('value');
            currentFilters.selectedType = type;
            currentFilters.selectedValue = value;
            loadTransactions(type, value);
        });

        // Modal close button
        $('.modal-close-button').on('click', function(e) {
            e.stopPropagation();
            console.log('closeModal called');
            closeModal();
        });

        // Close modal when clicking outside
        $(document).on('click', function(e) {
            if ($(e.target).is('#transactionModal')) {
                closeModal();
            }
        });

        // Close modal with ESC key
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Function to load transactions via AJAX
        // Pagination state for modal
        let modalPagination = {
            page: 1,
            per_page: 10
        };

        function loadTransactions(type, value, page = 1) {
            const startDate = currentFilters.start_date;
            const endDate = currentFilters.end_date;
            modalPagination.page = page;

            // Fade out table and show loading overlay
            $('#transactionsTable').css('opacity', 0.5);
            $('#transactionsLoadingOverlay').removeClass('hidden');

            $.ajax({
                url: "{{ route('transactions.index') }}",
                type: 'GET',
                data: {
                    [type]: value,
                    start_date: startDate,
                    end_date: endDate,
                    ajax: true,
                    page: modalPagination.page,
                    per_page: modalPagination.per_page
                },
                beforeSend: function() {
                    $('#transactionsPagination').html('');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalTitle').text(response.selectedName + ' Transactions');
                        $('#modalBalance').text(formatCurrency(response.selectedBalance))
                            .removeClass('text-green-600 text-red-600')
                            .addClass(response.selectedBalance >= 0 ? 'text-green-600' : 'text-red-600');

                        $('#bankColumnHeader').toggleClass('hidden', !response.hasBankColumn);

                        let transactionsHtml = '';
                        if (response.transactions.length > 0) {
                            response.transactions.forEach(function(transaction) {
                                transactionsHtml += `
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                            ${formatDate(transaction.date)}
                                        </td>
                                        ${response.hasBankColumn ? `
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                            ${transaction.bank_name || '-'}
                                        </td>
                                        ` : ''}
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                            ${capitalizeFirstLetter(transaction.transaction_type)}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm ${transaction.debit > 0 ? 'text-green-600 font-medium' : 'text-gray-500'}">
                                            ${transaction.debit ? formatCurrency(transaction.debit) : '-'}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm ${transaction.credit > 0 ? 'text-red-600 font-medium' : 'text-gray-500'}">
                                            ${transaction.credit ? formatCurrency(transaction.credit) : '-'}
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            transactionsHtml = `
                                <tr>
                                    <td colspan="${response.hasBankColumn ? 5 : 4}" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No transactions found in this period
                                    </td>
                                </tr>
                            `;
                        }
                        $('#transactionsTableBody').html(transactionsHtml);
                        renderTransactionsPagination(response.pagination);
                        // Fade in table and hide loading overlay
                        $('#transactionsTable').css('opacity', 1);
                        $('#transactionsLoadingOverlay').addClass('hidden');
                        // Scroll modal content to top
                        $('#transactionsTableContainer').animate({ scrollTop: 0 }, 200);
                        openModal();
                    } else {
                        alert('Failed to load transactions.');
                        $('#transactionsTable').css('opacity', 1);
                        $('#transactionsLoadingOverlay').addClass('hidden');
                    }
                },
                error: function(xhr) {
                    console.error('Transaction load error:', xhr.responseText);
                    $('#transactionsTableBody').html('<tr><td colspan="5" class="px-4 py-4 text-center text-sm text-red-600">Error loading transactions</td></tr>');
                    $('#transactionsTable').css('opacity', 1);
                    $('#transactionsLoadingOverlay').addClass('hidden');
                }
            });
        }

        function renderTransactionsPagination(pagination) {
            if (!pagination || pagination.last_page <= 1) {
                $('#transactionsPagination').html('');
                return;
            }
            let html = '';
            const prevDisabled = pagination.current_page === 1 ? 'disabled opacity-50' : '';
            const nextDisabled = pagination.current_page === pagination.last_page ? 'disabled opacity-50' : '';
            html += `<button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" id="transactionsPrevPage" ${prevDisabled}>Prev</button>`;
            html += `<span class="mx-2 text-sm">Page ${pagination.current_page} of ${pagination.last_page}</span>`;
            html += `<button class="px-3 py-1 rounded bg-gray-200 text-gray-700 hover:bg-gray-300" id="transactionsNextPage" ${nextDisabled}>Next</button>`;
            $('#transactionsPagination').html(html);

            // Event handlers
            $('#transactionsPrevPage').off('click').on('click', function() {
                if (pagination.current_page > 1) {
                    loadTransactions(currentFilters.selectedType, currentFilters.selectedValue, pagination.current_page - 1);
                }
            });
            $('#transactionsNextPage').off('click').on('click', function() {
                if (pagination.current_page < pagination.last_page) {
                    loadTransactions(currentFilters.selectedType, currentFilters.selectedValue, pagination.current_page + 1);
                }
            });
        }

        // Helper functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }

        function formatCurrency(amount) {
            return parseFloat(amount || 0).toFixed(2);
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function openModal() {
            $('#transactionModal').removeClass('hidden').addClass('opacity-100');
            $('#transactionModal').find('[aria-hidden="true"]').attr('aria-hidden', 'false');
        }

        function closeModal() {
            $('#transactionModal').addClass('hidden').removeClass('opacity-100');
            $('#transactionModal').find('[aria-hidden="false"]').attr('aria-hidden', 'true');
        }

        // Update balances dynamically
        function updateBalances(data) {
            console.log('Updating balances with:', data);
            $('.payment-method').each(function() {
                const $card = $(this);
                const type = $card.find('.transaction-link').data('type');
                const value = $card.find('.transaction-link').data('value');
                let balance = 0;

                if (type === 'all_banks') {
                    balance = data.allBanksBalance || 0;
                } else if (type === 'mode') {
                    if (value === 'cash') {
                        balance = data.cashBalance || 0;
                    } else if (value === 'credit') {
                        balance = data.creditBalance || 0;
                    } else {
                        balance = data.modeBalances[value] || 0;
                    }
                } else if (type === 'bank_id') {
                    balance = data.bankBalances[value]?.balance || 0;
                }

                console.log(`Updating ${type} ${value} balance to: ${balance}`);
                $card.find('.font-semibold')
                    .text(formatCurrency(balance))
                    .removeClass('text-green-600 text-red-600')
                    .addClass(balance >= 0 ? 'text-green-600' : 'text-red-600');
            });
        }

        // Initial fetch with current date
        $('#filterForm').submit();
    });
</script>
</body>
</html>