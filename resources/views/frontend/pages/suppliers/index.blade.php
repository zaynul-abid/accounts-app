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
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <h4 class="mb-4 text-2xl font-bold text-gray-800">Supplier Details</h4>

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
                    <i class="fas fa-info-circle"></i>
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
                    <div class="table-container">
                        <table class="table table-hover" id="transactionTable">
                            <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Date</th>
                                <th scope="col">Bill Number</th>
                                <th scope="col">Transaction </th>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        console.log("✅ JS is working and jQuery is ready!");

        // Handle supplier click
        $(document).on('click', '.supplier-list', function () {
            const supplierId = $(this).data('supplier-id');
            $('.supplier-list').removeClass('selected');
            $(this).addClass('selected');
            $('#supplierDetailsCard').show();

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

        // Function to fetch transactions
        function fetchTransactions(supplierId, page) {
            $.ajax({
                url: `/suppliers/${supplierId}/transactions?page=${page}`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        // Update supplier details
                        $('#supplierName').text(response.supplier.name);
                        $('#supplierContact').text(response.supplier.contact_number || '-');
                        $('#supplierAddress').text(response.supplier.address || '-');
                        $('#supplierBalance').text(parseFloat(response.supplier.opening_balance).toFixed(2));
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
                                    <td>${transaction.expense?.expense_type?.name || '-'}</td>
                                    <td>${parseFloat(transaction.debit || 0).toFixed(2)}</td>
                                    <td>${parseFloat(transaction.credit || 0).toFixed(2)}</td>
                                    <td>${transaction.notes || '-'}</td>
                                </tr>
                            `;
                            tbody.append(row);
                        });

                        // Calculate and update totals
                        let totalDebit = 0;
                        let totalCredit = 0;
                        response.transactions.forEach(transaction => {
                            totalDebit += parseFloat(transaction.debit || 0);
                            totalCredit += parseFloat(transaction.credit || 0);
                        });
                        const balance = totalDebit - totalCredit;

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

        // Initially hide supplier details
        $('#supplierDetailsCard').hide();
    });
</script>
</body>
</html>
