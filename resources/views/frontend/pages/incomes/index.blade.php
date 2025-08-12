<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Income Recording System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Inter', sans-serif;
            padding: 1rem;
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
            padding: 0.75rem 1rem;
            font-weight: 600;
            border-radius: 1rem 1rem 0 0;
            min-width: 0;
        }

        .card-header .d-flex > * {
            flex-shrink: 1;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        #bankAccountField {
            display: none;
        }

        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table th {
            background-color: #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: #4b5563;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.85rem;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .modal-content {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .narration-btn {
            font-size: 0.8rem;
            border-color: #17a2b8;
        }

        .form-control, .form-select {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            height: 2rem;
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
        }

        .input-group .btn {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        .income-type-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .income-type-container .form-select {
            flex-grow: 1;
        }

        .income-type-container .btn-add {
            flex-shrink: 0;
        }

        .form-check {
            margin-bottom: 0.35rem;
        }

        .form-check-input {
            margin-top: 0.2rem;
        }

        .card-body {
            padding: 1rem;
        }

        textarea.form-control {
            height: 3.5rem;
            resize: none;
        }

        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0.5rem;
            transition: background 0.3s ease;
        }

        .btn-add:hover {
            background: #218838;
        }

        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.35rem 0.9rem;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-outline-secondary {
            border-radius: 0.5rem;
            padding: 0.35rem 0.9rem;
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 600;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 0.5rem;
            padding: 0.35rem 0.9rem;
            font-size: 0.85rem;
            transition: background 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            align-items: center;
            white-space: nowrap;
        }

        .search-input-group {
            width: 200px !important;
        }

        .btn-narration-mobile {
            background: #17a2b8;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 0.5rem;
            transition: background 0.3s ease;
        }

        .btn-narration-mobile:hover {
            background: #138496;
        }

        .pagination {
            margin-top: 1rem;
        }

        .pagination .page-link {
            font-size: 0.85rem;
            padding: 0.3rem 0.6rem;
            border-radius: 0.5rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }
            .card-header {
                padding: 0.5rem;
            }
            .card-header .d-flex {
                flex-direction: row;
                gap: 0.5rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .form-control, .form-select {
                font-size: 0.8rem;
                height: 1.8rem;
            }
            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
            }
            #showAllButton {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }
            .table th, .table td {
                font-size: 0.75rem;
                padding: 0.5rem;
            }
            .table-container {
                max-height: 300px;
            }
            .input-group .btn {
                padding: 0.3rem 0.6rem;
            }
            .row.g-3 {
                gap: 0.5rem !important;
            }
            .action-buttons {
                flex-direction: row;
                gap: 0.2rem;
            }
            .action-buttons .btn {
                padding: 0.15rem 0.3rem;
                font-size: 0.7rem;
            }
            .search-input-group {
                width: 150px !important;
            }
            .income-type-container {
                display: flex;
                flex-direction: row; /* Keep row layout for tablet */
                align-items: center;
                gap: 0.5rem;
            }
            .income-type-container .form-select {
                flex-grow: 1;
                font-size: 0.8rem;
                height: 1.8rem;
            }
            .income-type-container .btn-add {
                flex-shrink: 0;
                padding: 0.3rem 0.6rem; /* Slightly smaller padding for tablet */
                font-size: 0.8rem;
                border-radius: 0.5rem;
            }
            .input-group .form-control {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }
            .input-group .btn {
                padding: 0.3rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0.25rem;
            }
            .card-header h6 {
                font-size: 0.9rem;
            }
            .card-header .d-flex {
                flex-direction: row;
                gap: 0.3rem;
            }
            .form-label {
                font-size: 0.75rem;
            }
            .btn-add, .btn-primary, .btn-secondary {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }
            .input-group .btn {
                padding: 0.25rem 0.5rem;
            }
            .table th, .table td {
                font-size: 0.7rem;
                padding: 0.4rem;
            }
            .badge {
                font-size: 0.7rem;
            }
            .modal-dialog {
                margin: 0.5rem;
            }
            .action-buttons {
                flex-direction: row;
                gap: 0.15rem;
            }
            .action-buttons .btn, .action-buttons .btn-narration-mobile {
                padding: 0.1rem 0.25rem;
                font-size: 0.65rem;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .search-input-group {
                width: 120px !important;
            }
            .input-group .form-control {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
            .input-group .btn {
                padding: 0.25rem 0.4rem;
                font-size: 0.7rem;
            }
            #showAllButton {
                font-size: 0.7rem;
                padding: 0.15rem 0.3rem;
            }
            .narration-column {
                display: none;
            }
            .income-type-container {
                display: flex;
                flex-direction: row; /* Keep row layout for mobile */
                align-items: center;
                gap: 0.3rem; /* Slightly smaller gap for mobile */
            }
            .income-type-container .form-select {
                flex-grow: 1;
                font-size: 0.75rem;
                height: 1.8rem;
            }
            .income-type-container .btn-add {
                flex-shrink: 0;
                padding: 0.25rem 0.5rem; /* Compact padding for mobile */
                font-size: 0.75rem;
                border-radius: 0.5rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h4 class="mb-3 text-xl font-bold text-gray-800">Income Recording System</h4>
    @if(auth()->user()->isEmployee())
        <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary mb-2">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    @else
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-2">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    @endif

    <!-- Record New Income Card -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Record New Income</h6>
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="card-body">
            <form id="incomeForm" method="POST" action="#">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="voucher_number" class="form-label required-field">Voucher Number</label>
                        <input type="text" class="form-control" id="voucher_number" name="voucher_number" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="income_type_id" class="form-label required-field">Income Type</label>
                        <div class="income-type-container">
                            <select class="form-select" id="income_type_id" name="income_type_id" required>
                                <option value="">Select Income Type</option>
                                @foreach($incomeTypes->sortBy('name') as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#incomeTypeModal" data-action="create">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="date_time" class="form-label required-field">Date</label>
                        <input type="date" class="form-control" id="date_time" name="date_time" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label required-field">Receipt Mode</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach(['cash', 'bank', 'credit', 'touch&go', 'boost', 'duitinow'] as $mode)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="receipt_mode" id="{{ $mode }}" value="{{ $mode }}" {{ $mode == 'cash' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $mode }}">{{ ucfirst(str_replace('&', ' & ', $mode)) }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="receipt_amount" class="form-label required-field">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="receipt_amount" name="receipt_amount" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="reference_note" class="form-label">Reference Note</label>
                        <input type="text" class="form-control" id="reference_note" name="reference_note">
                    </div>
                    <div class="col-12 col-md-6" id="bankAccountField">
                        <label for="bank_account_id" class="form-label required-field">Bank Account</label>
                        <div class="input-group">
                            <select class="form-select" id="bank_account_id" name="bank_account_id">
                                <option value="">Select Bank Account</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->account_name }} ({{ $account->account_number }})</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#bankAccountModal" data-action="create">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="narration" class="form-label">Narration</label>
                        <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter any additional details..."></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2 gap-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> Clear
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Record Income
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Income Records Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Income Records</h6>
            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <button class="btn btn-outline-primary btn-sm" id="showAllButton">Show All</button>
                <div class="input-group input-group-sm search-input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successAlert">
                Income recorded successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="table-container table-responsive">
                <table class="table table-hover" id="incomeTable">
                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">V-Number</th>
                        <th scope="col">Income</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Receipt Mode</th>
                        <th scope="col" class="narration-column">Narration</th>
                        @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                            <th scope="col">Actions</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody id="incomeTableBody">
                    @foreach($incomes as $index => $income)
                        <tr data-income-id="{{ $income->id }}">
                            <td>{{ $index + 1 + ($incomes->perPage() * ($incomes->currentPage() - 1)) }}</td>
                            <td>{{ $income->voucher_number }}</td>
                            <td>{{ $income->incomeType->name }}</td>
                            <td data-amount="{{ $income->receipt_amount }}">{{ number_format($income->receipt_amount, 2) }}</td>
                            <td>
                                <span class="badge {{
                                    $income->receipt_mode == 'cash' ? 'bg-success' :
                                    ($income->receipt_mode == 'bank' ? 'bg-primary' :
                                    ($income->receipt_mode == 'credit' ? 'bg-info' :
                                    ($income->receipt_mode == 'touch&go' ? 'bg-warning' :
                                    ($income->receipt_mode == 'boost' ? 'bg-dark' : 'bg-danger')))) }}">
                                    {{ ucfirst(str_replace('&', ' & ', $income->receipt_mode)) }}
                                </span>
                            </td>
                            <td class="narration-column">
                                <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="{{ $income->narration ?? '-' }}">
                                    View Narration
                                </button>
                            </td>
                            @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                                <td class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-narration-mobile rounded-3 d-block d-sm-none" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="{{ $income->narration ?? '-' }}">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning rounded-3 edit-income" data-income-id="{{ $income->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-3 delete-income" data-income-id="{{ $income->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 fw-semibold">
                Total Amount: <span id="totalAmount">0.00</span>
            </div>
            <nav class="mt-2 pagination-container" aria-label="Page navigation">
                {{ $incomes->appends(request()->except('page'))->links('vendor.pagination.bootstrap-5') }}
            </nav>
        </div>
    </div>

    <!-- Narration Modal -->
    <div class="modal fade" id="narrationModal" tabindex="-1" aria-labelledby="narrationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="narrationModalLabel">Narration Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="narrationContent" class="text-gray-700"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Type Modal -->
    <div class="modal fade" id="incomeTypeModal" tabindex="-1" aria-labelledby="incomeTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="incomeTypeForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Income Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formSuccess" class="alert alert-success d-none"></div>
                        <div id="formError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="nameField" class="form-label required-field">Income Type Name</label>
                            <input type="text" name="name" class="form-control" id="nameField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="descriptionField" class="form-label">Description</label>
                            <textarea name="description" class="form-control" id="descriptionField"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="typeField" class="form-label required-field">Type</label>
                            <select name="type" class="form-select" id="typeField" required>
                                <option value="Direct Income">Direct Income</option>
                                <option value="Indirect Income">Indirect Income</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="statusField" class="form-label required-field">Status</label>
                            <select name="status" class="form-select" id="statusField" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm" id="submitButton">Save</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bank Account Modal -->
    <div class="modal fade" id="bankAccountModal" tabindex="-1" aria-labelledby="bankAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="bankAccountForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bankModalTitle">Add New Bank Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="bankFormSuccess" class="alert alert-success d-none"></div>
                        <div id="bankFormError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="accountNameField" class="form-label required-field">Account Name</label>
                            <input type="text" name="account_name" class="form-control" id="accountNameField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="accountNumberField" class="form-label required-field">Account Number</label>
                            <input type="text" name="account_number" class="form-control" id="accountNumberField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="bankNameField" class="form-label required-field">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" id="bankNameField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="branchNameField" class="form-label">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control" id="branchNameField" />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="ifscCodeField" class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" id="ifscCodeField" />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="accountTypeField" class="form-label required-field">Account Type</label>
                            <select name="account_type" class="form-select" id="accountTypeField" required>
                                <option value="savings">Savings</option>
                                <option value="current">Current</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="isActiveField" class="form-label required-field">Status</label>
                            <select name="is_active" class="form-select" id="isActiveField" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm" id="bankSubmitButton">Save</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        console.log("✅ JS is working and jQuery is ready!");
        $('#income_type_id').select2({
            placeholder: "Select Income Type",
            allowClear: true,
            width: '100%'
        });

        $('#income_type_id').on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        let showAll = false;

        function updateTotalAmount() {
            let total = 0;
            $('#incomeTable tbody tr:visible').each(function() {
                const amount = parseFloat($(this).find('td[data-amount]').data('amount')) || 0;
                total += amount;
            });
            $('#totalAmount').text(total.toFixed(2));
        }

        function setCurrentDateTime() {
            const now = new Date();
            const formattedDate = now.toISOString().slice(0, 10);
            $('#date_time').val(formattedDate);
        }
        setCurrentDateTime();

        function updateReceiptModeFields() {
            const mode = $('input[name="receipt_mode"]:checked').val();
            $('#bankAccountField').toggle(mode === 'bank');
            if (mode !== 'bank') $('#bank_account_id').val('');
        }

        updateReceiptModeFields();

        $('input[name="receipt_mode"]').change(function() {
            updateReceiptModeFields();
        });

        $('#searchButton').click(function() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            $('#incomeTable tbody tr').each(function(index) {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.includes(searchTerm));
                if (rowText.includes(searchTerm)) {
                    $(this).find('td:first').text(index + 1);
                }
            });
            updateTotalAmount();
        });

        $('#searchInput').on('input', function() {
            if ($(this).val() === '') {
                loadIncomes($('.pagination .page-item.active .page-link').data('page') || 1, showAll);
            }
        });

        $('#showAllButton').click(function() {
            showAll = !showAll;
            $(this).text(showAll ? 'Show Today' : 'Show All');
            loadIncomes(1, showAll);
        });

        $('#narrationModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const narration = button.data('narration');
            $('#narrationContent').text(narration);
        });

        updateTotalAmount();

        $('#incomeTypeModal').on('show.bs.modal', function (event) {
            $('#incomeTypeForm')[0].reset();
            $('#formError').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#modalTitle').text('Add New Income Type');
        });

        $('#incomeTypeForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $('#submitButton');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('income-types.store') }}",
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#incomeTypeModal').modal('hide');
                        const $select = $('#income_type_id');
                        const newOption = new Option(
                            response.incomeType.name,
                            response.incomeType.id,
                            true,
                            true
                        );
                        $select.find(`option[value="${response.incomeType.id}"]`).remove();
                        $select.append(newOption).trigger('change');
                    });
                },
                error: function (xhr) {
                    let errorMessage = 'Something went wrong. Please try again.';
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(field, messages) {
                            const fieldId = field + 'Field';
                            if ($('#' + fieldId).length) {
                                $('#' + fieldId).addClass('is-invalid');
                                $('#' + fieldId).next('.invalid-feedback').text(messages[0]);
                            }
                            $.each(messages, function(index, message) {
                                errorHtml += '<li>' + message + '</li>';
                            });
                        });
                        errorHtml += '</ul>';
                        $('#formError').removeClass('d-none').html(errorHtml);
                    } else {
                        $('#formError').removeClass('d-none').html(errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        $('#bankAccountModal').on('show.bs.modal', function (event) {
            $('#bankAccountForm')[0].reset();
            $('#bankFormError').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#bankModalTitle').text('Add New Bank Account');
        });

        $('#bankAccountForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $('#bankSubmitButton');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('bank-accounts.store') }}",
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                },
                success: function (response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#bankAccountModal').modal('hide');
                        const $select = $('#bank_account_id');
                        const newOption = new Option(
                            `${response.bankAccount.account_name} (${response.bankAccount.account_number})`,
                            response.bankAccount.id,
                            true,
                            true
                        );
                        $select.find(`option[value="${response.bankAccount.id}"]`).remove();
                        $select.append(newOption).trigger('change');
                    });
                },
                error: function (xhr) {
                    let errorMessage = 'Something went wrong. Please try again.';
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(field, messages) {
                            const fieldId = field.replace('_', '') + 'Field';
                            if ($('#' + fieldId).length) {
                                $('#' + fieldId).addClass('is-invalid');
                                $('#' + fieldId).next('.invalid-feedback').text(messages[0]);
                            }
                            $.each(messages, function(index, message) {
                                errorHtml += '<li>' + message + '</li>';
                            });
                        });
                        errorHtml += '</ul>';
                        $('#bankFormError').removeClass('d-none').html(errorHtml);
                    } else {
                        $('#bankFormError').removeClass('d-none').html(errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        $('#incomeTypeModal, #bankAccountModal, #narrationModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0]?.reset();
            $(this).find('.alert').addClass('d-none').text('');
            $(this).find('.is-invalid').removeClass('is-invalid');
        });

        let isEditing = false;
        let editingIncomeId = null;

        $('#incomeForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            const url = isEditing ? `/incomes/${editingIncomeId}` : '/incomes';
            const method = isEditing ? 'PUT' : 'POST';

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#incomeForm')[0].reset();
                    $('#bankAccountField').hide();
                    $('#bank_account_id').val('');
                    submitBtn.html('<i class="fas fa-save me-1"></i> Record Income');
                    isEditing = false;
                    editingIncomeId = null;
                    setCurrentDateTime();
                    $('input[name="receipt_mode"][value="cash"]').prop('checked', true).trigger('change');

                    Swal.fire({
                        title: 'Success!',
                        text: response.message || (isEditing ? 'Income updated successfully' : 'Income recorded successfully'),
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    loadIncomes($('.pagination .page-item.active .page-link').data('page') || 1, showAll);
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || Object.values(xhr.responseJSON.errors || {}).join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        $(document).on('click', '.edit-income', function() {
            const incomeId = $(this).data('income-id');
            $.ajax({
                url: `/incomes/${incomeId}/edit`,
                method: 'GET',
                success: function(response) {
                    if (response.income) {
                        const income = response.income;
                        $('#income_type_id').val(income.income_type_id).trigger('change');
                        $('#voucher_number').val(income.voucher_number);
                        $('#reference_note').val(income.reference_note || '');
                        $('#bank_account_id').val(income.bank_account_id || '');
                        const storedDate = new Date(income.date_time).toISOString().slice(0, 10);
                        $('#date_time').val(storedDate);
                        $(`input[name="receipt_mode"][value="${income.receipt_mode}"]`).prop('checked', true).trigger('change');
                        $('#receipt_amount').val(income.receipt_amount);
                        $('#narration').val(income.narration || '');
                        isEditing = true;
                        editingIncomeId = incomeId;
                        $('#incomeForm button[type="submit"]').html('<i class="fas fa-save me-1"></i> Update Income');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch income details.'
                    });
                }
            });
        });

        $(document).on('click', '.delete-income', function() {
            const incomeId = $(this).data('income-id');
            const $row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this income record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/incomes/${incomeId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.redirect) {
                                window.location.href = response.redirectUrl;
                            } else {
                                $row.fadeOut(300, function() {
                                    $(this).remove();
                                    loadIncomes($('.pagination .page-item.active .page-link').data('page') || 1, showAll);
                                });
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Income deleted successfully',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Failed to delete income.'
                            });
                        }
                    });
                }
            });
        });

        function loadIncomes(page, showAll) {
            const searchTerm = $('#searchInput').val();
            $.ajax({
                url: '{{ route("incomes.index") }}',
                method: 'GET',
                data: { page: page, search: searchTerm, show_all: showAll ? 1 : 0 },
                success: function(response) {
                    $('#incomeTableBody').html($(response).find('#incomeTableBody').html());
                    $('.pagination-container').html($(response).find('.pagination-container').html());
                    updateTotalAmount();
                    attachPaginationListeners();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load incomes.'
                    });
                }
            });
        }

        function attachPaginationListeners() {
            $('.pagination .page-link').off('click').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page') || $(this).attr('href').split('page=')[1];
                if (page) {
                    loadIncomes(page, showAll);
                }
            });
        }

        attachPaginationListeners();

        function addIncomeToTable(income) {
            const formattedAmount = parseFloat(income.receipt_amount).toFixed(2);
            const badgeClass = income.receipt_mode === 'cash' ? 'bg-success' :
                income.recept_mode === 'bank' ? 'bg-primary' :
                    income.receipt_mode === 'credit' ? 'bg-info' :
                        income.receipt_mode === 'touch&go' ? 'bg-warning' :
                            income.receipt_mode === 'boost' ? 'bg-danger' : 'bg-secondary';
            const formattedMode = income.receipt_mode.replace('&', ' & ').replace(/(^\w|\s\w)/g, c => c.toUpperCase());
            const rowCount = $('#incomeTable tbody tr').length + 1;

            const newRow = `
            <tr data-income-id="${income.id}">
                <td>${rowCount}</td>
                <td>${income.voucher_number}</td>
                <td>${income.income_type?.name || 'N/A'}</td>
                <td data-amount="${income.receipt_amount}">${formattedAmount}</td>
                <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                <td class="narration-column">
                    <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${income.narration || '-'}">
                        View Narration
                    </button>
                </td>
                @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
            <td class="action-buttons">
                <button type="button" class="btn btn-sm btn-narration-mobile rounded-3 d-block d-sm-none" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${income.narration || '-'}">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="btn btn-sm btn-warning rounded-3 edit-income" data-income-id="${income.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger rounded-3 delete-income" data-income-id="${income.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                @endif
            </tr>
            `;
            $('#incomeTable tbody').prepend(newRow);
            $('#incomeTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        function updateIncomeInTable(income) {
            const formattedAmount = parseFloat(income.receipt_amount).toFixed(2);
            const badgeClass = income.receipt_mode === 'cash' ? 'bg-success' :
                income.receipt_mode === 'bank' ? 'bg-primary' :
                    income.receipt_mode === 'credit' ? 'bg-info' :
                        income.receipt_mode === 'touch&go' ? 'bg-warning' :
                            income.receipt_mode === 'boost' ? 'bg-danger' : 'bg-secondary';
            const formattedMode = income.receipt_mode.replace('&', ' & ').replace(/(^\w|\s\w)/g, c => c.toUpperCase());

            const $row = $(`#incomeTable tbody tr[data-income-id="${income.id}"]`);
            if ($row.length) {
                $row.html(`
                <td></td>
                <td>${income.voucher_number}</td>
                <td>${income.income_type?.name || 'N/A'}</td>
                <td data-amount="${income.receipt_amount}">${formattedAmount}</td>
                <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                <td class="narration-column">
                    <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${income.narration || '-'}">
                        View Narration
                    </button>
                </td>
                @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                <td class="action-buttons">
                    <button type="button" class="btn btn-sm btn-narration-mobile rounded-3 d-block d-sm-none" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${income.narration || '-'}">
                        <i class="fas fa-comment"></i>
                    </button>
                    <button class="btn btn-sm btn-warning rounded-3 edit-income" data-income-id="${income.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger rounded-3 delete-income" data-income-id="${income.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                @endif
                `);
            } else {
                addIncomeToTable(income);
            }
            $('#incomeTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
            updateTotalAmount();
        }
    });
</script>
</body>
</html>
