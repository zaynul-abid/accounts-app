@extends('backend.layouts.app')
@section('title', 'Expense Types')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Expense Types')
@section('sub-header', 'Expense Types')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Expense Types</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#expenseTypeModal" data-action="create">
                                    Add New Expense Type
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
                                    @forelse($expenseTypes as $index => $expenseType)
                                        <tr data-expense-id="{{ $expenseType->id }}">
                                            <td>{{ $expenseTypes->firstItem() + $index }}.</td>
                                            <td>{{ $expenseType->name }}</td>
                                            <td>{{ $expenseType->description ?? 'N/A' }}</td>
                                            <td>{{ $expenseType->type }}</td>
                                            <td style="color: {{ $expenseType->status ? 'green' : 'red' }}">
                                                {{ $expenseType->status ? 'Active' : 'Inactive' }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-expense-type"
                                                            data-toggle="modal"
                                                            data-target="#expenseTypeModal"
                                                            data-action="edit"
                                                            data-id="{{ $expenseType->id }}"
                                                            data-name="{{ $expenseType->name }}"
                                                            data-description="{{ $expenseType->description ?? '' }}"
                                                            data-type="{{ $expenseType->type }}"
                                                            data-status="{{ $expenseType->status }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-expense-type"
                                                            data-id="{{ $expenseType->id }}"
                                                            data-status="{{ $expenseType->status }}"
                                                            data-name="{{ $expenseType->name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No expense types found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($expenseTypes->hasPages())
                            <div class="card-footer clearfix">
                                {{ $expenseTypes->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Expense Type Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="expenseTypeModal" tabindex="-1" role="dialog" aria-labelledby="expenseTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="expenseTypeForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="expense_type_id" id="expenseTypeId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Expense Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Success Message -->
                        <div id="formSuccess" class="alert alert-success d-none"></div>

                        <!-- Error Message -->
                        <div id="formError" class="alert alert-danger d-none"></div>

                        <!-- Expense Type Name -->
                        <div class="form-group">
                            <label for="nameField">Expense Type Name</label>
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
                                <option value="Direct Expense">Direct Expense</option>
                                <option value="Indirect Expense">Indirect Expense</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="statusField">Status</label>
                            <select name="status" class="form-control" id="statusField" required>
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

            // Handle modal show event (for create only)
            $('#expenseTypeModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                if (action === 'create') {
                    $('#expenseTypeForm')[0].reset();
                    $('#formMethod').val('POST');
                    $('#expenseTypeId').val('');
                    $('#modalTitle').text('Add New Expense Type');
                    $('#formError').addClass('d-none').text('');
                    $('.is-invalid').removeClass('is-invalid');
                }
            });

            // Handle edit expense type button click
            $(document).on('click', '.edit-expense-type', function () {
                const expenseTypeId = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description') || '';
                const type = $(this).data('type');
                const status = $(this).data('status');

                // Populate the modal form fields
                $('#modalTitle').text('Edit Expense Type');
                $('#formMethod').val('PUT');
                $('#expenseTypeId').val(expenseTypeId);
                $('#nameField').val(name);
                $('#descriptionField').val(description);
                $('#typeField').val(type);
                $('#statusField').val(status ? '1' : '0');

                // Clear any previous error messages and invalid states
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Handle expense type form submission
            $('#expenseTypeForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const expenseTypeId = $('#expenseTypeId').val();
                const url = isEdit ? `/expense-types/${expenseTypeId}` : "{{ route('expense-types.store') }}";

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
                                $('#expenseTypeModal').modal('hide');
                                if (isEdit) {
                                    updateExpenseTypeInTable(response.expenseType);
                                } else {
                                    addExpenseTypeToTable(response.expenseType);
                                }
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
                                const fieldId = field + 'Field';
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
                        $('#formError').removeClass('d-none').html(errorMessage);
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

            // Clear form when modal is closed
            $('#expenseTypeModal').on('hidden.bs.modal', function () {
                $('#expenseTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#expenseTypeId').val('');
                $('#modalTitle').text('Add New Expense Type');
            });

            // Function to add new expense type to the table
            function addExpenseTypeToTable(expenseType) {
                const index = $('table tbody tr').length;
                const newRow = `
                    <tr data-expense-id="${expenseType.id}">
                        <td>${index + 1}.</td>
                        <td>${expenseType.name}</td>
                        <td>${expenseType.description || 'N/A'}</td>
                        <td>${expenseType.type}</td>
                        <td style="color: ${expenseType.status ? 'green' : 'red'}">
                            ${expenseType.status ? 'Active' : 'Inactive'}
                        </td>
                        <td>
                            <div class="d-flex flex-nowrap align-items-center">
                                <button class="btn btn-sm btn-warning mr-1 edit-expense-type"
                                        data-toggle="modal"
                                        data-target="#expenseTypeModal"
                                        data-action="edit"
                                        data-id="${expenseType.id}"
                                        data-name="${expenseType.name}"
                                        data-description="${expenseType.description || ''}"
                                        data-type="${expenseType.type}"
                                        data-status="${expenseType.status}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-expense-type"
                                        data-id="${expenseType.id}"
                                        data-status="${expenseType.status}"
                                        data-name="${expenseType.name}">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('table tbody').prepend(newRow);
                // Remove empty table message if present
                if ($('tbody tr td').text() === 'No expense types found.') {
                    $('tbody').empty();
                    $('tbody').append(newRow);
                }
            }

            // Function to update expense type in the table
            function updateExpenseTypeInTable(expenseType) {
                const row = $(`tr[data-expense-id="${expenseType.id}"]`);
                if (row.length) {
                    row.find('td:eq(1)').text(expenseType.name);
                    row.find('td:eq(2)').text(expenseType.description || 'N/A');
                    row.find('td:eq(3)').text(expenseType.type);
                    row.find('td:eq(4)').css('color', expenseType.status ? 'green' : 'red')
                        .text(expenseType.status ? 'Active' : 'Inactive');
                    row.find('.edit-expense-type').data('name', expenseType.name)
                        .data('description', expenseType.description || '')
                        .data('type', expenseType.type)
                        .data('status', expenseType.status);
                }
            }

            // Handle delete expense type
            $(document).on('click', '.delete-expense-type', function () {
                const expenseTypeId = $(this).data('id');
                const expenseTypeName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${expenseTypeName}". This cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/expense-types/${expenseTypeId}`,
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
                                    });
                                    $(`tr[data-expense-id="${expenseTypeId}"]`).fadeOut(300, function() {
                                        $(this).remove();
                                        if ($('tbody tr').length === 0) {
                                            $('tbody').html('<tr><td colspan="6" class="text-center">No expense types found.</td></tr>');
                                        }
                                    });
                                }
                            },
                            error: function (xhr) {
                                let errorMessage = 'Failed to delete expense type';
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
