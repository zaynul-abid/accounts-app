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
                                        <tr>
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
                                                            data-description="{{ $expenseType->description }}"
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

            // Handle modal show event (for both create and edit)
            $('#expenseTypeModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#expenseTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');

                if (action === 'edit') {
                    $('#modalTitle').text('Edit Expense Type');
                    $('#formMethod').val('PUT');

                    const expenseTypeId = button.data('id');
                    $('#expenseTypeId').val(expenseTypeId);
                    $('#nameField').val(button.data('name'));
                    $('#descriptionField').val(button.data('description'));
                    $('#typeField').val(button.data('type'));
                    $('#statusField').val(button.data('status'));

                    // If status is inactive (0), clear the selection
                    if (button.data('status') == 0) {
                        $('#statusField').val('');
                    }
                } else {
                    $('#modalTitle').text('Add New Expense Type');
                    $('#formMethod').val('POST');
                    $('#expenseTypeId').val('');
                }
            });

            // Handle form submission
            $('#expenseTypeForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const method = $('#formMethod').val();
                const expenseTypeId = $('#expenseTypeId').val();

                $('#formError').addClass('d-none').text('');
                $('#expenseTypeForm input, #expenseTypeForm select, #expenseTypeForm textarea').removeClass('is-invalid');

                let url = "{{ route('expense-types.store') }}";
                if (method === 'PUT') {
                    url = "{{ route('expense-types.update', '') }}/" + expenseTypeId;
                }

                $.ajax({
                    url: url,
                    method: method === 'PUT' ? 'POST' : 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        Swal.fire({
                            title: method === 'PUT' ? 'Updated!' : 'Created!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#expenseTypeModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorHtml = '<ul class="mb-0">';
                            $.each(errors, function(field, messages) {
                                $('#' + field + 'Field').addClass('is-invalid');
                                $.each(messages, function(index, message) {
                                    errorHtml += '<li>' + message + '</li>';
                                });
                            });
                            errorHtml += '</ul>';
                            $('#formError').removeClass('d-none').html(errorHtml);
                        } else {
                            $('#formError').removeClass('d-none')
                                .html('Something went wrong. Please try again.<br>Error: ' +
                                    (xhr.responseJSON.message || xhr.statusText));
                        }
                    }
                });
            });

            // Clear form when modal is closed
            $('#expenseTypeModal').on('hidden.bs.modal', function () {
                $('#expenseTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Delete expense type handler
            $(document).on('click', '.delete-expense-type', function() {
                const expenseTypeId = $(this).data('id');
                const expenseTypeStatus = $(this).data('status');
                const expenseTypeName = $(this).data('name');

                if (expenseTypeStatus == 1) {
                    Swal.fire({
                        title: 'Cannot Delete Active Expense Type',
                        text: `The expense type "${expenseTypeName}" is currently active. Please deactivate it first before deleting.`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the expense type "${expenseTypeName}". This action cannot be undone.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('expense-types.destroy', '') }}/" + expenseTypeId,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    $(`button.delete-expense-type[data-id="${expenseTypeId}"]`)
                                        .closest('tr').fadeOut(300, function() {
                                        $(this).remove();
                                        if ($('tbody tr').length === 0) {
                                            $('tbody').html(
                                                '<tr><td colspan="6" class="text-center">No expense types found.</td></tr>'
                                            );
                                        }
                                    });
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON.message || 'Something went wrong',
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
