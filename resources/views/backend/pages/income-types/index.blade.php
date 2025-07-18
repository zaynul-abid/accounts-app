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
                                        <tr data-income-id="{{ $incomeType->id }}">
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
                                                            data-description="{{ $incomeType->description ?? '' }}"
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
                            </div .<div>
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

            // Handle modal show event (for create only)
            $('#incomeTypeModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                if (action === 'create') {
                    $('#incomeTypeForm')[0].reset();
                    $('#formMethod').val('POST');
                    $('#incomeTypeId').val('');
                    $('#modalTitle').text('Add New Income Type');
                    $('#formError').addClass('d-none').text('');
                    $('.is-invalid').removeClass('is-invalid');
                }
            });

            // Handle edit income type button click
            $(document).on('click', '.edit-income-type', function () {
                const incomeTypeId = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description') || '';
                const type = $(this).data('type');
                const status = $(this).data('status');

                // Populate the modal form fields
                $('#modalTitle').text('Edit Income Type');
                $('#formMethod').val('PUT');
                $('#incomeTypeId').val(incomeTypeId);
                $('#nameField').val(name);
                $('#descriptionField').val(description);
                $('#typeField').val(type);
                $('#statusField').val(status ? '1' : '0');

                // Clear any previous error messages and invalid states
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Handle income type form submission
            $('#incomeTypeForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const incomeTypeId = $('#incomeTypeId').val();
                const url = isEdit ? `/income-types/${incomeTypeId}` : "{{ route('income-types.store') }}";

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
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#incomeTypeModal').modal('hide');
                                if (isEdit) {
                                    updateIncomeTypeInTable(response.incomeType);
                                } else {
                                    addIncomeTypeToTable(response.incomeType);
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
                                const fieldId = field + 'Name';
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
            $('#incomeTypeModal').on('hidden.bs.modal', function () {
                $('#incomeTypeForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#incomeTypeId').val('');
                $('#modalTitle').text('Add New Income Type');
            });

            // Function to add new income type to the table
            function addIncomeTypeToTable(incomeType) {
                const index = $('table tbody tr').length;
                const newRow = `
                    <tr data-income-id="${incomeType.id}">
                        <td>${index + 1}.</td>
                        <td>${incomeType.name}</td>
                        <td>${incomeType.description || 'N/A'}</td>
                        <td>${incomeType.type}</td>
                        <td style="color: ${incomeType.status ? 'green' : 'red'}">
                            ${incomeType.status ? 'Active' : 'Inactive'}
                        </td>
                        <td>
                            <div class="d-flex flex-nowrap align-items-center">
                                <button class="btn btn-sm btn-warning mr-1 edit-income-type"
                                        data-toggle="modal"
                                        data-target="#incomeTypeModal"
                                        data-action="edit"
                                        data-id="${incomeType.id}"
                                        data-name="${incomeType.name}"
                                        data-description="${incomeType.description || ''}"
                                        data-type="${incomeType.type}"
                                        data-status="${incomeType.status}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-income-type"
                                        data-id="${incomeType.id}"
                                        data-status="${incomeType.status}"
                                        data-name="${incomeType.name}">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('table tbody').prepend(newRow);
            }

            // Function to update income type in the table
            function updateIncomeTypeInTable(incomeType) {
                const row = $(`tr[data-income-id="${incomeType.id}"]`);
                if (row.length) {
                    row.find('td:eq(1)').text(incomeType.name);
                    row.find('td:eq(2)').text(incomeType.description || 'N/A');
                    row.find('td:eq(3)').text(incomeType.type);
                    row.find('td:eq(4)').css('color', incomeType.status ? 'green' : 'red')
                        .text(incomeType.status ? 'Active' : 'Inactive');
                    row.find('.edit-income-type').data('name', incomeType.name)
                        .data('description', incomeType.description || '')
                        .data('type', incomeType.type)
                        .data('status', incomeType.status);
                }
            }

            // Handle delete income type
            $(document).on('click', '.delete-income-type', function () {
                const incomeTypeId = $(this).data('id');
                const incomeTypeName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${incomeTypeName}". This cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/income-types/${incomeTypeId}`,
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
                                    $(`tr[data-income-id="${incomeTypeId}"]`).remove();
                                }
                            },
                            error: function (xhr) {
                                let errorMessage = 'Failed to delete income type';
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
