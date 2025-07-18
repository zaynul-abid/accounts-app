@extends('backend.layouts.app')
@section('title', 'Bank Accounts')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Bank Accounts')
@section('sub-header', 'Bank Accounts')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Bank Accounts</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#bankAccountModal" data-action="create">
                                    Add New Bank Account
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                        <th>Bank Name</th>
                                        <th>Branch Name</th>
                                        <th>IFSC Code</th>
                                        <th>Account Type</th>
                                        <th>Status</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($bankAccounts as $index => $bankAccount)
                                        <tr data-bank-account-id="{{ $bankAccount->id }}">
                                            <td>{{ $bankAccounts->firstItem() + $index }}.</td>
                                            <td>{{ $bankAccount->account_name }}</td>
                                            <td>{{ $bankAccount->account_number }}</td>
                                            <td>{{ $bankAccount->bank_name }}</td>
                                            <td>{{ $bankAccount->branch_name ?? 'N/A' }}</td>
                                            <td>{{ $bankAccount->ifsc_code ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($bankAccount->account_type) }}</td>
                                            <td style="color: {{ $bankAccount->is_active ? 'green' : 'red' }}">
                                                {{ $bankAccount->is_active ? 'Active' : 'Inactive' }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-bank-account"
                                                            data-toggle="modal"
                                                            data-target="#bankAccountModal"
                                                            data-action="edit"
                                                            data-id="{{ $bankAccount->id }}"
                                                            data-account-name="{{ $bankAccount->account_name }}"
                                                            data-account-number="{{ $bankAccount->account_number }}"
                                                            data-bank-name="{{ $bankAccount->bank_name }}"
                                                            data-branch-name="{{ $bankAccount->branch_name }}"
                                                            data-ifsc-code="{{ $bankAccount->ifsc_code }}"
                                                            data-account-type="{{ $bankAccount->account_type }}"
                                                            data-is-active="{{ $bankAccount->is_active ? 1 : 0 }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-bank-account"
                                                            data-id="{{ $bankAccount->id }}"
                                                            data-account-name="{{ $bankAccount->account_name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No bank accounts found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($bankAccounts->hasPages())
                            <div class="card-footer clearfix">
                                {{ $bankAccounts->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Bank Account Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="bankAccountModal" tabindex="-1" role="dialog" aria-labelledby="bankAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="bankAccountForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="bank_account_id" id="bankAccountId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Bank Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Success Message -->
                        <div id="formSuccess" class="alert alert-success d-none"></div>

                        <!-- Error Message -->
                        <div id="formError" class="alert alert-danger d-none"></div>

                        <!-- Account Name -->
                        <div class="form-group">
                            <label for="accountNameField">Account Name</label>
                            <input type="text" name="account_name" class="form-control" id="accountNameField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label for="accountNumberField">Account Number</label>
                            <input type="text" name="account_number" class="form-control" id="accountNumberField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Bank Name -->
                        <div class="form-group">
                            <label for="bankNameField">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" id="bankNameField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label for="branchNameField">Branch Name</label>
                            <input type="text" name="branch_name" class="form-control" id="branchNameField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label for="ifscCodeField">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" id="ifscCodeField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Account Type -->
                        <div class="form-group">
                            <label for="accountTypeField">Account Type</label>
                            <select name="account_type" class="form-control" id="accountTypeField" required>
                                <option value="savings">Savings</option>
                                <option value="current">Current</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="isActiveField">Status</label>
                            <select name="is_active" class="form-control" id="isActiveField" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="submitButton">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            console.log("✅ JS is working and jQuery is ready!");

            // Handle modal show event (for create and edit)
            $('#bankAccountModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#bankAccountForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#modalTitle').text('Add New Bank Account');
                $('#isActiveField').val('1'); // Default to Active for new accounts

                if (action === 'edit') {
                    $('#modalTitle').text('Edit Bank Account');
                    $('#formMethod').val('PUT');
                    $('#bankAccountId').val(button.data('id'));
                    $('#accountNameField').val(button.data('account-name'));
                    $('#accountNumberField').val(button.data('account-number'));
                    $('#bankNameField').val(button.data('bank-name'));
                    $('#branchNameField').val(button.data('branch-name') || '');
                    $('#ifscCodeField').val(button.data('ifsc-code') || '');
                    $('#accountTypeField').val(button.data('account-type'));
                    $('#isActiveField').val(button.data('is-active').toString());
                }
            });

            // Handle bank account form submission
            $('#bankAccountForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const bankAccountId = $('#bankAccountId').val();
                const url = isEdit ? `/bank-accounts/${bankAccountId}` : '{{ route("bank-accounts.store") }}';

                console.log('Submitting form - URL:', url, 'Data:', Object.fromEntries(formData));

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: url,
                    method: 'POST', // Laravel handles PUT via _method
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: isEdit ? 'Updated!' : 'Created!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#bankAccountModal').modal('hide');
                                location.reload(); // Refresh the page
                            });
                        }
                    },
                    error: function (xhr) {
                        console.log('AJAX Error:', xhr);
                        let errorMessage = 'Something went wrong. Please try again.';
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors || {};
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function (field, messages) {
                                const fieldId = field.replace('_', '') + 'Field';
                                if ($('#' + fieldId).length) {
                                    $('#' + fieldId).addClass('is-invalid');
                                    $('#' + fieldId).next('.invalid-feedback').text(messages[0]);
                                }
                                $.each(messages, function (index, message) {
                                    errorHtml += '<li>' + message + '</li>';
                                });
                            });
                            errorHtml += '</ul>';
                            errorMessage = errorHtml;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage,
                            icon: 'error'
                        });
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Clear form when modal is closed
            $('#bankAccountModal').on('hidden.bs.modal', function () {
                $('#bankAccountForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#bankAccountId').val('');
                $('#modalTitle').text('Add New Bank Account');
            });

            // Delete bank account handler with confirmation
            $(document).on('click', '.delete-bank-account', function () {
                const bankAccountId = $(this).data('id');
                const accountName = $(this).data('account-name');

                console.log('Delete clicked - Bank Account ID:', bankAccountId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the bank account "${accountName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/bank-accounts/${bankAccountId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload(); // Refresh the page
                                    });
                                }
                            },
                            error: function (xhr) {
                                console.log('Delete AJAX Error:', xhr);
                                let errorMessage = 'Failed to delete bank account';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
