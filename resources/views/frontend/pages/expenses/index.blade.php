<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Recording System</title>
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
        #supplierField {
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
            padding: 0.3rem 0.6rem;
            font-size: 0.85rem;
        }
        .narration-btn {
            font-size: 0.85rem;
            border-color: #17a2b8;
        }
        .form-control, .form-select {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
            height: 2.2rem;
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
        }
        .input-group .btn {
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
            border-radius: 0 0.5rem 0.5rem 0;
        }
        .form-check {
            margin-bottom: 0.4rem;
        }
        .form-check-input {
            margin-top: 0.2rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        textarea.form-control {
            height: 4rem;
            resize: none;
        }
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.4rem 0.8rem;
            font-size: 0.9rem;
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
            padding: 0.4rem 1rem;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-outline-secondary {
            border-radius: 0.5rem;
            padding: 0.4rem 1rem;
        }
        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container">
    <h4 class="mb-4 text-2xl font-bold text-gray-800">Expense Recording System</h4>

    <!-- Record New Expense Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Record New Expense</h6>
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="card-body">
            <form id="expenseForm" method="POST" action="#">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="expense_type_id" class="form-label required-field">Expense Type</label>
                        <div class="input-group">
                            <select class="form-select" id="expense_type_id" name="expense_type_id" required>
                                <option value="">Select Expense Type</option>
                                @foreach($expenseTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#expenseTypeModal" data-action="create">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="voucher_number" class="form-label required-field">Voucher Number</label>
                        <input type="text" class="form-control" id="voucher_number" name="voucher_number" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date_time" class="form-label required-field">Date & Time</label>
                        <input type="datetime-local" class="form-control" id="date_time" name="date_time" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label required-field">Payment Mode</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_mode" id="cash" value="cash" checked>
                                <label class="form-check-label" for="cash">Cash</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_mode" id="credit" value="credit">
                                <label class="form-check-label" for="credit">Credit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_mode" id="bank" value="bank">
                                <label class="form-check-label" for="bank">Bank</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="reference_note" class="form-label">Reference Note</label>
                        <input type="text" class="form-control" id="reference_note" name="reference_note">
                    </div>
                    <div class="col-md-4">
                        <label for="payment_amount" class="form-label required-field">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="payment_amount" name="payment_amount" required>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="toggle_supplier" value="1">
                            <label class="form-check-label" for="toggle_supplier">Is Supplier Involved?</label>
                        </div>
                    </div>
                    <div class="col-md-4" id="supplierField">
                        <label for="supplier_id" class="form-label required-field">Supplier</label>
                        <div class="input-group">
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-add" data-bs-toggle="modal" data-bs-target="#supplierModal" data-action="create">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="narration" class="form-label">Narration</label>
                        <textarea class="form-control" id="narration" name="narration" rows="3" placeholder="Enter any additional details..."></textarea>
                    </div>
                    <div class="col-md-4" id="bankAccountField">
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
                </div>
                <div class="d-flex justify-content-end mt-3 gap-2">
                    <button type="reset" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> Clear
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Record Expense
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Expense Records Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Expense Records</h6>
            <div class="input-group w-25">
                <input type="text" class="form-control" id="searchInput" placeholder="Search...">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="successAlert">
                Expense recorded successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="table-container">
                <table class="table table-hover" id="expenseTable">
                    <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Voucher Number</th>
                        <th scope="col">Expense</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment Mode</th>
                        <th scope="col">Supplier</th>
                        <th scope="col">Narration</th>
                        @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                            <th scope="col">Actions</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expenses as $index => $expense)
                        <tr data-expense-id="{{ $expense->id }}">
                            <td>{{ $index + 1 + ($expenses->perPage() * ($expenses->currentPage() - 1)) }}</td>
                            <td>{{ $expense->voucher_number }}</td>
                            <td>{{ $expense->expenseType->name }}</td>
                            <td data-amount="{{ $expense->payment_amount }}">{{ number_format($expense->payment_amount, 2) }}</td>
                            <td>
                                <span class="badge {{ $expense->payment_mode == 'cash' ? 'bg-success' : ($expense->payment_mode == 'bank' ? 'bg-primary' : 'bg-info') }}">{{ ucfirst($expense->payment_mode) }}</span>
                            </td>
                            <td>{{ $expense->supplier->name ?? 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="{{ $expense->narration ?? '-' }}">
                                    View Narration
                                </button>
                            </td>
                            @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                                <td>
                                    <button class="btn btn-sm btn-warning rounded-3 me-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger rounded-3">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 fw-semibold">
                Total Amount: <span id="totalAmount">0.00</span>
            </div>
            <nav class="mt-3" aria-label="Page navigation">
                {{ $expenses->links() }}
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

    <!-- Expense Type Modal -->
    <div class="modal fade" id="expenseTypeModal" tabindex="-1" aria-labelledby="expenseTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="expenseTypeForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Expense Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="formSuccess" class="alert alert-success d-none"></div>
                        <div id="formError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="nameField" class="form-label required-field">Expense Type Name</label>
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
                                <option value="Direct Expense">Direct Expense</option>
                                <option value="Indirect Expense">Indirect Expense</option>
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

    <!-- Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="supplierForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="supplierModalTitle">Add New Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="supplierFormSuccess" class="alert alert-success d-none"></div>
                        <div id="supplierFormError" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label for="supplierNameField" class="form-label required-field">Name</label>
                            <input type="text" name="name" class="form-control" id="supplierNameField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="supplierContactField" class="form-label required-field">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" id="supplierContactField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="supplierAddressField" class="form-label">Address</label>
                            <textarea name="address" class="form-control" id="supplierAddressField" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="supplierOpeningField" class="form-label required-field">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" id="supplierOpeningField" required />
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="supplierStatusField" class="form-label required-field">Status</label>
                            <select name="status" class="form-select" id="supplierStatusField" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm" id="supplierSubmitButton">Save</button>
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
<script>
    $(document).ready(function () {
        console.log("✅ JS is working and jQuery is ready!");

        // Calculate and update total amount
        function updateTotalAmount() {
            let total = 0;
            $('#expenseTable tbody tr:visible').each(function() {
                const amount = parseFloat($(this).find('td[data-amount]').data('amount')) || 0;
                total += amount;
            });
            $('#totalAmount').text(total.toFixed(2));
        }

        // Set current date and time
        function setCurrentDateTime() {
            const now = new Date();
            const formattedDateTime = now.toISOString().slice(0, 16);
            $('#date_time').val(formattedDateTime);
        }
        setCurrentDateTime();

        // Show/hide fields based on payment mode
        function updatePaymentModeFields() {
            const mode = $('input[name="payment_mode"]:checked').val();
            $('#bankAccountField').toggle(mode === 'bank');
            if (mode !== 'bank') $('#bank_account_id').val('');
        }

        // Show/hide supplier field based on checkbox
        $('#toggle_supplier').change(function() {
            $('#supplierField').toggle(this.checked);
            if (!this.checked) {
                $('#supplier_id').val('');
            }
        });

        // Initial call to set default state
        updatePaymentModeFields();

        // Bind change event
        $('input[name="payment_mode"]').change(function() {
            updatePaymentModeFields();
        });

        // Search functionality
        $('#searchButton').click(function() {
            const searchTerm = $('#searchInput').val().toLowerCase();
            $('#expenseTable tbody tr').each(function(index) {
                const rowText = $(this).text().toLowerCase();
                $(this).toggle(rowText.includes(searchTerm));
                if (rowText.includes(searchTerm)) {
                    $(this).find('td:first').text(index + 1);
                }
            });
            updateTotalAmount();
        });

        // Clear search on input clear
        $('#searchInput').on('input', function() {
            if ($(this).val() === '') {
                $('#expenseTable tbody tr').show();
                $('#expenseTable tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
                updateTotalAmount();
            }
        });

        // Narration modal handling
        $('#narrationModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const narration = button.data('narration');
            $('#narrationContent').text(narration);
        });

        // Initial total amount calculation
        updateTotalAmount();

        // Expense Type Modal handling
        $('#expenseTypeModal').on('show.bs.modal', function (event) {
            $('#expenseTypeForm')[0].reset();
            $('#formError').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#modalTitle').text('Add New Expense Type');
        });

        // Expense Type Form submission
        $('#expenseTypeForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $('#submitButton');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('expense-types.store') }}",
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
                        $('#expenseTypeModal').modal('hide');
                        const $select = $('#expense_type_id');
                        const newOption = new Option(
                            response.expenseType.name,
                            response.expenseType.id,
                            true,
                            true
                        );
                        $select.find(`option[value="${response.expenseType.id}"]`).remove();
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

        // Bank Account Modal handling
        $('#bankAccountModal').on('show.bs.modal', function (event) {
            $('#bankAccountForm')[0].reset();
            $('#bankFormError').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#bankModalTitle').text('Add New Bank Account');
        });

        // Bank Account Form submission
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

        // Supplier Modal handling
        $('#supplierModal').on('show.bs.modal', function (event) {
            $('#supplierForm')[0].reset();
            $('#supplierFormError').addClass('d-none').text('');
            $('.is-invalid').removeClass('is-invalid');
            $('#supplierModalTitle').text('Add New Supplier');
        });

        // Supplier Form submission
        $('#supplierForm').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = $('#supplierSubmitButton');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

            $.ajax({
                url: "{{ route('suppliers.store') }}",
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
                        $('#supplierModal').modal('hide');
                        const $select = $('#supplier_id');
                        const newOption = new Option(
                            response.supplier.name,
                            response.supplier.id,
                            true,
                            true
                        );
                        $select.find(`option[value="${response.supplier.id}"]`).remove();
                        $select.append(newOption).trigger('change');
                    });
                },
                error: function (xhr) {
                    let errorMessage = 'Something went wrong. Please try again.';
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        $.each(errors, function(field, messages) {
                            const fieldId = 'supplier' + field.charAt(0).toUpperCase() + field.slice(1) + 'Field';
                            if ($('#' + fieldId).length) {
                                $('#' + fieldId).addClass('is-invalid');
                                $('#' + fieldId).next('.invalid-feedback').text(messages[0]);
                            }
                            $.each(messages, function(index, message) {
                                errorHtml += '<li>' + message + '</li>';
                            });
                        });
                        errorHtml += '</ul>';
                        $('#supplierFormError').removeClass('d-none').html(errorHtml);
                    } else {
                        $('#supplierFormError').removeClass('d-none').html(errorMessage);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        // Clear forms when modals are closed
        $('#expenseTypeModal, #bankAccountModal, #narrationModal, #supplierModal').on('hidden.bs.modal', function () {
            $(this).find('form')[0]?.reset();
            $(this).find('.alert').addClass('d-none').text('');
            $(this).find('.is-invalid').removeClass('is-invalid');
        });

        // Expense Form submission
        let isEditing = false;
        let editingExpenseId = null;

        $('#expenseForm').submit(function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const submitBtn = $(this).find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            const url = isEditing ? `/expenses/${editingExpenseId}` : '/expenses';
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
                    $('#expenseForm')[0].reset();
                    $('#bankAccountField').hide();
                    $('#bank_account_id').val('');
                    $('#supplierField').hide();
                    $('#supplier_id').val('');
                    $('#toggle_supplier').prop('checked', false);
                    submitBtn.html('<i class="fas fa-save me-1"></i> Record Expense');
                    isEditing = false;
                    editingExpenseId = null;
                    setCurrentDateTime();
                    $('input[name="payment_mode"][value="cash"]').prop('checked', true).trigger('change');

                    Swal.fire({
                        title: 'Success!',
                        text: response.message || (isEditing ? 'Expense updated successfully' : 'Expense recorded successfully'),
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    if (response.expense) {
                        $(`#expenseTable tbody tr[data-expense-id="${response.expense.id}"]`).remove();
                        if (isEditing) {
                            updateExpenseInTable(response.expense);
                        } else {
                            addExpenseToTable(response.expense);
                        }
                    }
                    $('#searchInput').val('').trigger('input');
                    updateTotalAmount();
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

        // Edit button click
        $(document).on('click', '.btn-warning', function() {
            const expenseId = $(this).closest('tr').data('expense-id');
            $.ajax({
                url: `/expenses/${expenseId}/edit`,
                method: 'GET',
                success: function(response) {
                    if (response.expense) {
                        const expense = response.expense;
                        $('#expense_type_id').val(expense.expense_type_id).trigger('change');
                        $('#voucher_number').val(expense.voucher_number);
                        $('#reference_note').val(expense.reference_note || '');
                        $('#bank_account_id').val(expense.bank_account_id || '');
                        $('#toggle_supplier').prop('checked', !!expense.supplier_id).trigger('change');
                        if (expense.supplier_id) $('#supplier_id').val(expense.supplier_id);
                        const storedDateTime = new Date(expense.date_time);
                        $('#date_time').val(storedDateTime.toISOString().slice(0, 16));
                        $(`input[name="payment_mode"][value="${expense.payment_mode}"]`).prop('checked', true).trigger('change');
                        $('#payment_amount').val(expense.payment_amount);
                        $('#narration').val(expense.narration || '');
                        isEditing = true;
                        editingExpenseId = expenseId;
                        $('#expenseForm button[type="submit"]').html('<i class="fas fa-save me-1"></i> Update Expense');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch expense details.'
                    });
                }
            });
        });

        // Delete button click
        $(document).on('click', '.btn-danger', function() {
            const expenseId = $(this).closest('tr').data('expense-id');
            const $row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this expense record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/expenses/${expenseId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                $('#expenseTable tbody tr:visible').each(function(index) {
                                    $(this).find('td:first').text(index + 1);
                                });
                                updateTotalAmount();
                                if ($('table tbody tr').length === 0 && response.redirect) {
                                    window.location.href = response.redirectUrl;
                                }
                            });
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message || 'Expense deleted successfully',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Failed to delete expense.'
                            });
                        }
                    });
                }
            });
        });

        // Add new expense to table
        function addExpenseToTable(expense) {
            const date = new Date(expense.date_time);
            const formattedDate = date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }).replace(',', '');
            const formattedAmount = parseFloat(expense.payment_amount).toFixed(2);
            const badgeClass = expense.payment_mode === 'cash' ? 'bg-success' : (expense.payment_mode === 'bank' ? 'bg-primary' : 'bg-info');
            const formattedMode = expense.payment_mode.charAt(0).toUpperCase() + expense.payment_mode.slice(1);
            const rowCount = $('#expenseTable tbody tr').length + 1;

            const newRow = `
            <tr data-expense-id="${expense.id}">
                <td>${rowCount}</td>
                <td>${expense.voucher_number}</td>
                <td>${expense.expense_type?.name || 'N/A'}</td>
                <td data-amount="${expense.payment_amount}">${formattedAmount}</td>
                <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                <td>${expense.supplier?.name || 'N/A'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${expense.narration || '-'}">
                        View Narration
                    </button>
                </td>
                @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
            <td>
                <button class="btn btn-sm btn-warning rounded-3 me-1">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger rounded-3">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
@endif
            </tr>
            `;
            $('#expenseTable tbody').prepend(newRow);
            $('#expenseTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Update expense in table
        function updateExpenseInTable(expense) {
            const date = new Date(expense.date_time);
            const formattedDate = date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }).replace(',', '');
            const formattedAmount = parseFloat(expense.payment_amount).toFixed(2);
            const badgeClass = expense.payment_mode === 'cash' ? 'bg-success' : (expense.payment_mode === 'bank' ? 'bg-primary' : 'bg-info');
            const formattedMode = expense.payment_mode.charAt(0).toUpperCase() + expense.payment_mode.slice(1);

            const $row = $(`#expenseTable tbody tr[data-expense-id="${expense.id}"]`);
            if ($row.length) {
                $row.html(`
                <td></td>
                <td>${expense.voucher_number}</td>
                <td>${expense.expense_type?.name || 'N/A'}</td>
                <td data-amount="${expense.payment_amount}">${formattedAmount}</td>
                <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                <td>${expense.supplier?.name || 'N/A'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info narration-btn" data-bs-toggle="modal" data-bs-target="#narrationModal" data-narration="${expense.narration || '-'}">
                        View Narration
                    </button>
                </td>
                @if(auth()->check() && (auth()->user()->usertype === 'admin' || auth()->user()->usertype === 'superadmin'))
                <td>
                    <button class="btn btn-sm btn-warning rounded-3 me-1">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger rounded-3">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                @endif
                `);
            } else {
                addExpenseToTable(expense);
            }
            $('#expenseTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
            updateTotalAmount();
        }
    });
</script>
</body>
</html>
