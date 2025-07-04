@extends('backend.layouts.app')
@section('title', 'Income Types')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Income Types')
@section('sub-header', 'Income Types')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Income Types</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#incomeTypeModal" data-action="create">
                                    Add New Income Type
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
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($incomeTypes as $index => $incomeType)
                                        <tr>
                                            <td>{{ $incomeTypes->firstItem() + $index }}.</td>
                                            <td>{{ $incomeType->name }}</td>
                                            <td>{{ $incomeType->description ?? 'N/A' }}</td>
                                            <td>{{ $incomeType->type }}</td>
                                            <td style="color: {{ $incomeType->status ? 'green' : 'red' }}">
                                                {{ $incomeType->status ? 'Active' : 'Inactive' }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-income-type"
                                                            data-toggle="modal"
                                                            data-target="#incomeTypeModal"
                                                            data-action="edit"
                                                            data-id="{{ $incomeType->id }}"
                                                            data-name="{{ $incomeType->name }}"
                                                            data-description="{{ $incomeType->description }}"
                                                            data-type="{{ $incomeType->type }}"
                                                            data-status="{{ $incomeType->status }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-income-type"
                                                            data-id="{{ $incomeType->id }}"
                                                            data-status="{{ $incomeType->status }}"
                                                            data-name="{{ $incomeType->name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No income types found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($incomeTypes->hasPages())
                            <div class="card-footer clearfix">
                                {{ $incomeTypes->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Income Type Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="incomeTypeModal" tabindex="-1" role="dialog" aria-labelledby="incomeTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="incomeTypeForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="income_type_id" id="incomeTypeId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Income Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Success Message -->
                        <div id="formSuccess" class="alert alert-success d-none"></div>

                        <!-- Error Message -->
                        <div id="formError" class="alert alert-danger d-none"></div>

                        <!-- Income Type Name -->
                        <div class="form-group">
                            <label for="nameField">Income Type Name</label>
                            <input type="text" name="name" class="form-control" id="nameField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="descriptionField">Description</label>
                            <textarea name="description" class="form-control" id="descriptionField"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Type -->
                        <div class="form-group">
                            <label for="typeField">Type</label>
                            <select name="type" class="form-control" id="typeField" required>
                                <option value="Direct Income">Direct Income</option>
                                <option value="Indirect Income">Indirect Income</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="statusField">Status</label>
                            <select name="status" class="form-control" id="statusField">
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

            // Show/hide cheque number field based on receipt mode
            $('input[name="receipt_mode"]').change(function() {
                $('#chequeNumberField').toggle($(this).val() === 'cheque');
                if ($(this).val() !== 'cheque') {
                    $('#cheque_number').val('');
                }
            });

            // Handle modal show event (for create only)
            $('#incomeTypeModal').on('show.bs.modal', function (event) {
                $('#incomeTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#modalTitle').text('Add New Income Type');
            });

            // Handle income type form submission
            $('#incomeTypeForm').on('submit', function (e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();

                // Show loading state
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

                            // Update the select dropdown
                            const $select = $('#income_type_id');

                            // Create and append the new option
                            const newOption = new Option(
                                response.incomeType.name,
                                response.incomeType.id,
                                true,
                                true
                            );

                            // Remove if already exists to avoid duplicates
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

            // Clear form when modal is closed
            $('#incomeTypeModal').on('hidden.bs.modal', function () {
                $('#incomeTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Handle income form submission
            $('#incomeForm').submit(function(e) {
                e.preventDefault();

                // Get form data
                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalBtnText = submitBtn.html();
                const isEdit = $(this).data('edit-mode') === true;
                const incomeId = $(this).data('income-id');

                // Show loading state
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processing...');

                const url = isEdit ? `/incomes/${incomeId}` : '/incomes';
                const method = isEdit ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        // Reset form
                        $('#incomeForm')[0].reset();
                        $('#chequeNumberField').hide();
                        $('#cheque_number').val('');
                        $('#incomeForm').data('edit-mode', false).removeData('income-id');

                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update or add the record to the table
                        if (isEdit) {
                            updateIncomeInTable(response.income);
                        } else {
                            addIncomeToTable(response.income);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred. Please try again.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON.errors) {
                                errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> ' + (isEdit ? 'Update Income' : 'Record Income'));
                    }
                });
            });

            // Function to add new income to the table
            function addIncomeToTable(income) {
                // Format the date to match your existing format (d M Y h:i A)
                const date = new Date(income.date_time);
                const formattedDate = date.toLocaleDateString('en-US', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).replace(',', '');

                // Format amount with 2 decimal places
                const formattedAmount = parseFloat(income.receipt_amount).toFixed(2);

                // Determine badge color based on receipt mode
                let badgeClass = 'bg-info';
                if (income.receipt_mode === 'cash') badgeClass = 'bg-success';
                if (income.receipt_mode === 'cheque') badgeClass = 'bg-primary';

                // Format receipt mode (capitalize first letter)
                const formattedMode = income.receipt_mode.charAt(0).toUpperCase() + income.receipt_mode.slice(1);

                // Create the new row HTML
                const newRow = `
                <tr data-income-id="${income.id}">
                    <td>${income.id}</td>
                    <td>${income.income_type?.name || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                    <td>$${formattedAmount}</td>
                    <td>${income.cheque_number || '-'}</td>
                    <td>${income.created_by?.name || 'System'}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-1 view-income" data-income-id="${income.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1 edit-income" data-income-id="${income.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-income" data-income-id="${income.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

                // Prepend the new row to the table (to show at top)
                $('table tbody').prepend(newRow);
            }

            // Function to update income in the table
            function updateIncomeInTable(income) {
                // Format the date to match your existing format (d M Y h:i A)
                const date = new Date(income.date_time);
                const formattedDate = date.toLocaleDateString('en-US', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }).replace(',', '');

                // Format amount with 2 decimal places
                const formattedAmount = parseFloat(income.receipt_amount).toFixed(2);

                // Determine badge color based on receipt mode
                let badgeClass = 'bg-info';
                if (income.receipt_mode === 'cash') badgeClass = 'bg-success';
                if (income.receipt_mode === 'cheque') badgeClass = 'bg-primary';

                // Format receipt mode (capitalize first letter)
                const formattedMode = income.receipt_mode.charAt(0).toUpperCase() + income.receipt_mode.slice(1);

                // Update the row
                const row = $(`tr[data-income-id="${income.id}"]`);
                row.html(`
                <td>${income.id}</td>
                <td>${income.income_type?.name || 'N/A'}</td>
                <td>${formattedDate}</td>
                <td><span class="badge ${badgeClass}">${formattedMode}</span></td>
                <td>$${formattedAmount}</td>
                <td>${income.cheque_number || '-'}</td>
                <td>${income.created_by?.name || 'System'}</td>
                <td>
                    <button class="btn btn-sm btn-info me-1 view-income" data-income-id="${income.id}">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning me-1 edit-income" data-income-id="${income.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-income" data-income-id="${income.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `);
            }

            // Edit income handler
            $(document).on('click', '.edit-income', function() {
                const incomeId = $(this).data('income-id');

                $.ajax({
                    url: `/incomes/${incomeId}/edit`,
                    method: 'GET',
                    success: function(response) {
                        const income = response.income;

                        // Fill the form with income data
                        $('#income_type_id').val(income.income_type_id);

                        // Format date for datetime-local input
                        const date = new Date(income.date_time);
                        const formattedDate = date.toISOString().slice(0, 16);
                        $('#date_time').val(formattedDate);

                        $(`input[name="receipt_mode"][value="${income.receipt_mode}"]`).prop('checked', true);
                        $('#receipt_amount').val(income.receipt_amount);
                        $('#cheque_number').val(income.cheque_number || '');
                        $('#narration').val(income.narration || '');

                        // Show cheque number field if mode is cheque
                        if (income.receipt_mode === 'cheque') {
                            $('#chequeNumberField').show();
                        } else {
                            $('#chequeNumberField').hide();
                        }

                        // Change form to edit mode
                        $('#incomeForm')
                            .data('edit-mode', true)
                            .data('income-id', incomeId)
                            .find('button[type="submit"]')
                            .html('<i class="fas fa-save me-1"></i> Update Income');

                        // Scroll to form
                        $('html, body').animate({
                            scrollTop: $('#incomeForm').offset().top - 20
                        }, 500);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load income data'
                        });
                    }
                });
            });

            // Delete income handler with confirmation
            $(document).on('click', '.delete-income', function() {
                const incomeId = $(this).data('income-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/incomes/${incomeId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );

                                // Remove the row from the table
                                $(`tr[data-income-id="${incomeId}"]`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete income',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // View income handler (can be implemented similarly)
            $(document).on('click', '.view-income', function() {
                const incomeId = $(this).data('income-id');
                // Implement view functionality as needed
                alert('View income with ID: ' + incomeId);
            });
        });
    </script>
@endsection
