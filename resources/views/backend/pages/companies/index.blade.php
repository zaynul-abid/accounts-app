@extends('backend.layouts.app')
@section('title', 'Companies')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Companies')
@section('sub-header', 'Companies')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Companies</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#companyModal" data-action="create">
                                    Add New Company
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
                                        <th>Place</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Description</th>
                                        <th>Tax ID</th>
                                        <th>Status</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($companies as $index => $company)
                                        <tr data-company-id="{{ $company->id }}">
                                            <td>{{ $companies->firstItem() + $index }}.</td>
                                            <td>{{ $company->name }}</td>
                                            <td>{{ $company->place ?? 'N/A' }}</td>
                                            <td>{{ $company->phone ?? 'N/A' }}</td>
                                            <td>{{ $company->address ?? 'N/A' }}</td>
                                            <td>{{ $company->description ?? 'N/A' }}</td>
                                            <td>{{ $company->tax_id ?? 'N/A' }}</td>
                                            <td style="color: {{ $company->status === 'active' ? 'green' : 'red' }}">
                                                {{ ucfirst($company->status) }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-company"
                                                            data-toggle="modal"
                                                            data-target="#companyModal"
                                                            data-action="edit"
                                                            data-id="{{ $company->id }}"
                                                            data-name="{{ $company->name }}"
                                                            data-place="{{ $company->place }}"
                                                            data-phone="{{ $company->phone }}"
                                                            data-address="{{ $company->address }}"
                                                            data-description="{{ $company->description }}"
                                                            data-tax-id="{{ $company->tax_id }}"
                                                            data-status="{{ $company->status }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-company"
                                                            data-id="{{ $company->id }}"
                                                            data-name="{{ $company->name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No companies found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($companies->hasPages())
                            <div class="card-footer clearfix">
                                {{ $companies->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Company Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="companyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="companyForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="company_id" id="companyId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Company</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Success Message -->
                        <div id="formSuccess" class="alert alert-success d-none"></div>

                        <!-- Error Message -->
                        <div id="formError" class="alert alert-danger d-none"></div>

                        <!-- Name -->
                        <div class="form-group">
                            <label for="nameField">Name</label>
                            <input type="text" name="name" class="form-control" id="nameField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Place -->
                        <div class="form-group">
                            <label for="placeField">Place</label>
                            <input type="text" name="place" class="form-control" id="placeField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phoneField">Phone</label>
                            <input type="text" name="phone" class="form-control" id="phoneField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="addressField">Address</label>
                            <textarea name="address" class="form-control" id="addressField"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="descriptionField">Description</label>
                            <textarea name="description" class="form-control" id="descriptionField"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Tax ID -->
                        <div class="form-group">
                            <label for="taxIdField">Tax ID</label>
                            <input type="text" name="tax_id" class="form-control" id="taxIdField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="statusField">Status</label>
                            <select name="status" class="form-control" id="statusField" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
            $('#companyModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#companyForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#modalTitle').text('Add New Company');

                if (action === 'edit') {
                    $('#modalTitle').text('Edit Company');
                    $('#formMethod').val('PUT');
                    $('#companyId').val(button.data('id'));
                    $('#nameField').val(button.data('name'));
                    $('#placeField').val(button.data('place') || '');
                    $('#phoneField').val(button.data('phone') || '');
                    $('#addressField').val(button.data('address') || '');
                    $('#descriptionField').val(button.data('description') || '');
                    $('#taxIdField').val(button.data('tax-id') || '');
                    $('#statusField').val(button.data('status'));
                }
            });

            // Handle company form submission
            $('#companyForm').on('submit', function (e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const companyId = $('#companyId').val();
                const url = isEdit ? `/companies/${companyId}` : '{{ route("companies.store") }}';
                const method = isEdit ? 'POST' : 'POST'; // Laravel uses POST with _method=PUT for updates

                // Show loading state
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#companyModal').modal('hide');
                            if (isEdit) {
                                updateCompanyInTable(response.company);
                            } else {
                                location.reload(); // For create, reload to update table
                            }
                        });
                    },
                    error: function (xhr) {
                        console.error('AJAX Error:', xhr); // Log error for debugging
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

            // Function to update company in the table
            function updateCompanyInTable(company) {
                const row = $(`tr[data-company-id="${company.id}"]`);
                row.html(`
                    <td>${row.find('td:first').text()}</td>
                    <td>${company.name}</td>
                    <td>${company.place || 'N/A'}</td>
                    <td>${company.phone || 'N/A'}</td>
                    <td>${company.address || 'N/A'}</td>
                    <td>${company.description || 'N/A'}</td>
                    <td>${company.tax_id || 'N/A'}</td>
                    <td style="color: ${company.status === 'active' ? 'green' : 'red'}">
                        ${company.status.charAt(0).toUpperCase() + company.status.slice(1)}
                    </td>
                    <td>
                        <div class="d-flex flex-nowrap align-items-center">
                            <button class="btn btn-sm btn-warning mr-1 edit-company"
                                    data-toggle="modal"
                                    data-target="#companyModal"
                                    data-action="edit"
                                    data-id="${company.id}"
                                    data-name="${company.name}"
                                    data-place="${company.place}"
                                    data-phone="${company.phone}"
                                    data-address="${company.address}"
                                    data-description="${company.description}"
                                    data-tax-id="${company.tax_id}"
                                    data-status="${company.status}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-company"
                                    data-id="${company.id}"
                                    data-name="${company.name}">
                                Delete
                            </button>
                        </div>
                    </td>
                `);
            }

            // Clear form when modal is closed
            $('#companyModal').on('hidden.bs.modal', function () {
                $('#companyForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Delete company handler with confirmation
            $(document).on('click', '.delete-company', function() {
                const companyId = $(this).data('id');
                const companyName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the company "${companyName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/companies/${companyId}`,
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
                                $(`tr[data-company-id="${companyId}"]`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to delete company',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
