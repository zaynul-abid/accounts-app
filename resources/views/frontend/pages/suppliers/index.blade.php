<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            padding: 1.5rem;
        }
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #ffffff;
        }
        .card-header {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            border-bottom: none;
            padding: 1rem;
            font-weight: 600;
            border-radius: 1rem 1rem 0 0;
        }
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .table th {
            background-color: #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #4b5563;
        }
        .table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .badge {
            padding: 0.4em 0.7em;
            font-weight: 500;
            font-size: 0.85rem;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .btn-sm {
            padding: 0.3rem 0.6rem;
            font-size: 0.85rem;
        }
        .supplier-list {
            cursor: pointer;
        }
        .supplier-list:hover {
            background-color: #f1f3f5;
        }
        .selected {
            background-color: #e9ecef;
            font-weight: 600;
        }
        .supplier-details {
            display: none;
        }
        .btn-outline-secondary {
            border-radius: 0.5rem;
            padding: 0.4rem 1rem;
        }
        .totals-section {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .balance-positive {
            color: #28a745;
        }
        .balance-negative {
            color: #dc3545;
        }
        .date-filter-card {
            margin-bottom: 1rem;
            padding: 1rem;
            background: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .date-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }
        .date-filter-form .form-group {
            flex: 1;
            min-width: 150px;
        }
        .date-filter-form .btn {
            height: 38px;
        }
        .invalid-feedback {
            font-size: 0.8rem;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <h4 class="mb-4 text-2xl font-bold text-gray-800">Supplier Details</h4>
    @if(auth()->user()->isEmployee())
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    @else
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    @endif

    <div class="row">
        <!-- Supplier List Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Suppliers</h6>
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="table table-hover" id="supplierTable">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($suppliers as $supplier)
                                <tr class="supplier-list" data-supplier-id="{{ $supplier->id }}">
                                    <td>{{ $supplier->name }}</td>
                                    <td>
                                        <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $supplier->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Supplier Details and Transactions Card -->
        <div class="col-md-8">
            <div class="card supplier-details" id="supplierDetailsCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0" id="supplierName">Select a Supplier</h6>
                    <div>
                        <button class="btn btn-primary btn-sm" id="makePaymentBtn" data-bs-toggle="modal" data-bs-target="#paymentModal" disabled>
                            <i class="fas fa-money-bill-wave"></i> Make Payment
                        </button>
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Contact Number:</strong> <span id="supplierContact">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Address:</strong> <span id="supplierAddress">-</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Opening Balance:</strong> <span id="supplierBalance">-</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> <span id="supplierStatus">-</span>
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3">Transaction History</h6>
                    <div class="date-filter-card">
                        <form id="dateFilterForm" class="date-filter-form">
                            <div class="form-group">
                                <label for="fromDate">From Date</label>
                                <input type="date" class="form-control" id="fromDate" name="from_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group">
                                <label for="toDate">To Date</label>
                                <input type="date" class="form-control" id="toDate" name="to_date">
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm" id="filterBtn">Filter</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilterBtn">Clear</button>
                        </form>
                    </div>
                    <div class="table-container">
                        <table class="table table-hover" id="transactionTable">
                            <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Bill Number</th>
                                <th scope="col">Transaction Type</th>
                                <th scope="col">Transaction Mode</th>
                                <th scope="col">Debit</th>
                                <th scope="col">Credit</th>
                                <th scope="col">Notes</th>
                            </tr>
                            </thead>
                            <tbody id="transactionTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="totals-section mt-3">
                        <div>Total Debit: <span id="totalDebit">0.00</span></div>
                        <div>Total Credit: <span id="totalCredit">0.00</span></div>
                        <div>Balance: <span id="totalBalance" class="balance-positive">0.00</span></div>
                    </div>
                    <nav class="mt-3" aria-label="Page navigation" id="pagination">
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Make Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        <input type="hidden" id="supplierId" name="supplier_id">
                        <div class="mb-3">
                            <label for="paymentAmount" class="form-label">Payment Amount</label>
                            <input type="number" class="form-control" id="paymentAmount" name="payment_amount" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="paymentDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="paymentDate" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="paymentNote" class="form-label">Note</label>
                            <textarea class="form-control" id="paymentNote" name="note" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        console.log("✅ JS is working and jQuery is ready!");

        // Validate date range
        function validateDateRange() {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const filterBtn = $('#filterBtn');
            let isValid = true;

            $('.invalid-feedback').text('').hide();
            $('#fromDate, #toDate').removeClass('is-invalid');

            if (fromDate && toDate && fromDate > toDate) {
                $('#fromDate').addClass('is-invalid');
                $('#fromDate').next('.invalid-feedback').text('From date cannot be after To date').show();
                isValid = false;
            }

            filterBtn.prop('disabled', !isValid);
            return isValid;
        }

        // Handle input changes for date validation
        $('#fromDate, #toDate').on('change', validateDateRange);

        // Handle supplier click
        $(document).on('click', '.supplier-list', function () {
            const supplierId = $(this).data('supplier-id');
            $('.supplier-list').removeClass('selected');
            $(this).addClass('selected');
            $('#supplierDetailsCard').show();
            $('#makePaymentBtn').prop('disabled', false);
            $('#supplierId').val(supplierId);
            $('#dateFilterForm')[0].reset(); // Reset date filter
            $('#fromDate, #toDate').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
            $('#filterBtn').prop('disabled', false);

            // Fetch supplier transactions
            fetchTransactions(supplierId, 1);
        });

        // Handle pagination click
        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            const page = $(this).data('page');
            const supplierId = $('.supplier-list.selected').data('supplier-id');
            if (!supplierId || !page) return;

            fetchTransactions(supplierId, page);
        });

        // Handle date filter form submission
        $('#dateFilterForm').on('submit', function (e) {
            e.preventDefault();
            if (!validateDateRange()) return;

            const supplierId = $('.supplier-list.selected').data('supplier-id');
            if (!supplierId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Supplier Selected',
                    text: 'Please select a supplier first.'
                });
                return;
            }

            fetchTransactions(supplierId, 1); // Reset to page 1 on filter
        });

        // Handle clear filter button
        $('#clearFilterBtn').on('click', function () {
            $('#dateFilterForm')[0].reset();
            $('#fromDate, #toDate').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
            $('#filterBtn').prop('disabled', false);

            const supplierId = $('.supplier-list.selected').data('supplier-id');
            if (!supplierId) return;

            fetchTransactions(supplierId, 1); // Reset to page 1
        });

        // Function to fetch transactions
        function fetchTransactions(supplierId, page) {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
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
                success: function (response) {
                    if (response.success) {
                        // Update supplier details
                        $('#supplierName').text(response.supplier.name || 'Unknown');
                        $('#supplierContact').text(response.supplier.contact_number || '-');
                        $('#supplierAddress').text(response.supplier.address || '-');
                        $('#supplierBalance').text(parseFloat(response.supplier.opening_balance || 0).toFixed(2));
                        $('#supplierStatus').html(`<span class="badge ${response.supplier.status ? 'bg-success' : 'bg-danger'}">${response.supplier.status ? 'Active' : 'Inactive'}</span>`);

                        // Update transactions table
                        const tbody = $('#transactionTableBody');
                        tbody.empty();
                        response.transactions.forEach((transaction, index) => {
                            const date = transaction.date ? new Date(transaction.date).toLocaleDateString('en-US', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }) : '-';
                            const row = `
                                <tr>
                                    <td>${index + 1 + (response.pagination.per_page * (response.pagination.current_page - 1))}</td>
                                    <td>${date}</td>
                                    <td>${transaction.bill_number || '-'}</td>
                                    <td>${transaction.transaction_type || '-'}</td>
                                    <td>${transaction.transaction_mode || '-'}</td>
                                    <td>${parseFloat(transaction.debit || 0).toFixed(2)}</td>
                                    <td>${parseFloat(transaction.credit || 0).toFixed(2)}</td>
                                    <td>${transaction.notes || '-'}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        // Update totals with fallback
                        const totals = response.totals || { total_debit: 0, total_credit: 0, balance: 0 };
                        const totalDebit = parseFloat(totals.total_debit || 0);
                        const totalCredit = parseFloat(totals.total_credit || 0);
                        const balance = parseFloat(totals.balance || 0);

                        $('#totalDebit').text(totalDebit.toFixed(2));
                        $('#totalCredit').text(totalCredit.toFixed(2));
                        $('#totalBalance').text(Math.abs(balance).toFixed(2));
                        $('#totalBalance').removeClass('balance-positive balance-negative');
                        $('#totalBalance').addClass(balance >= 0 ? 'balance-positive' : 'balance-negative');

                        // Update pagination
                        const pagination = $('#pagination');
                        pagination.empty();
                        if (response.pagination.last_page > 1) {
                            let paginationHtml = `
                                <ul class="pagination">
                                    <li class="page-item ${response.pagination.current_page === 1 ? 'disabled' : ''}">
                                        <a class="page-link" href="#" data-page="${response.pagination.current_page - 1}">Previous</a>
                                    </li>
                            `;
                            for (let i = 1; i <= response.pagination.last_page; i++) {
                                paginationHtml += `
                                    <li class="page-item ${response.pagination.current_page === i ? 'active' : ''}">
                                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                                    </li>
                                `;
                            }
                            paginationHtml += `
                                    <li class="page-item ${response.pagination.current_page === response.pagination.last_page ? 'disabled' : ''}">
                                        <a class="page-link" href="#" data-page="${response.pagination.current_page + 1}">Next</a>
                                    </li>
                                </ul>
                            `;
                            pagination.html(paginationHtml);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch supplier transactions.'
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to fetch supplier transactions.'
                    });
                }
            });
        }

        // Handle payment form submission
        $('#paymentForm').on('submit', function (e) {
            e.preventDefault();
            const supplierId = $('#supplierId').val();
            const formData = {
                supplier_id: supplierId,
                payment_amount: $('#paymentAmount').val(),
                date: $('#paymentDate').val(),
                note: $('#paymentNote').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                url: `/suppliers/${supplierId}/transactions`,
                method: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payment recorded successfully!'
                        });
                        $('#paymentModal').modal('hide');
                        $('#paymentForm')[0].reset();
                        fetchTransactions(supplierId, 1); // Refresh transactions
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to record payment.'
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to record payment.'
                    });
                }
            });
        });

        // Initially hide supplier details and disable payment button
        $('#supplierDetailsCard').hide();
        $('#makePaymentBtn').prop('disabled', true);
    });
</script>
</body>
</html>
