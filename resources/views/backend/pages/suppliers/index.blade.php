@extends('backend.layouts.app')
@section('title', 'Suppliers')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Suppliers')
@section('sub-header', 'Suppliers')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Suppliers</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#supplierModal" data-action="create">
                                    Add New Supplier
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
                                        <th>Contact Number</th>
                                        <th>Address</th>
                                        <th>Opening Balance</th>
                                        <th>Status</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($suppliers as $index => $supplier)
                                        <tr data-supplier-id="{{ $supplier->id }}">
                                            <td>{{ $suppliers->firstItem() + $index }}.</td>
                                            <td>{{ $supplier->name }}</td>
                                            <td>{{ $supplier->contact_number }}</td>
                                            <td>{{ $supplier->address ?? 'N/A' }}</td>
                                            <td>{{ number_format($supplier->opening_balance, 2) }}</td>
                                            <td style="color: {{ $supplier->status ? 'green' : 'red' }}">
                                                {{ $supplier->status ? 'Active' : 'Inactive' }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-supplier"
                                                            data-toggle="modal"
                                                            data-target="#supplierModal"
                                                            data-action="edit"
                                                            data-id="{{ $supplier->id }}"
                                                            data-name="{{ $supplier->name }}"
                                                            data-contact-number="{{ $supplier->contact_number }}"
                                                            data-address="{{ $supplier->address }}"
                                                            data-opening-balance="{{ $supplier->opening_balance }}"
                                                            data-status="{{ $supplier->status ? 1 : 0 }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-supplier"
                                                            data-id="{{ $supplier->id }}"
                                                            data-name="{{ $supplier->name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No suppliers found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($suppliers->hasPages())
                            <div class="card-footer clearfix">
                                {{ $suppliers->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Supplier Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="supplierModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="supplierForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="supplier_id" id="supplierId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Supplier</h5>
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

                        <!-- Contact Number -->
                        <div class="form-group">
                            <label for="contactNumberField">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" id="contactNumberField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="addressField">Address</label>
                            <textarea name="address" class="form-control" id="addressField" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label for="openingBalanceField">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" id="openingBalanceField" required />
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

            // Handle modal show event (for create and edit)
            $('#supplierModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#supplierForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#modalTitle').text('Add New Supplier');

                if (action === 'edit') {
                    $('#modalTitle').text('Edit Supplier');
                    $('#formMethod').val('PUT');
                    $('#supplierId').val(button.data('id'));
                    $('#nameField').val(button.data('name'));
                    $('#contactNumberField').val(button.data('contact-number'));
                    $('#addressField').val(button.data('address') || '');
                    $('#openingBalanceField').val(button.data('opening-balance'));
                    $('#statusField').val(button.data('status').toString());
                }
            });

            // Handle supplier form submission
            $('#supplierForm').on('submit', function (e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const supplierId = $('#supplierId').val();
                const url = isEdit ? `/suppliers/${supplierId}` : '{{ route("suppliers.store") }}';
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
                            $('#supplierModal').modal('hide');
                            location.reload(); // Refresh the page after create or edit
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

            // Delete supplier handler with confirmation
            $(document).on('click', '.delete-supplier', function() {
                const supplierId = $(this).data('id');
                const supplierName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the supplier "${supplierName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/suppliers/${supplierId}`,
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Refresh the page after delete
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to delete supplier',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Clear form when modal is closed
            $('#supplierModal').on('hidden.bs.modal', function () {
                $('#supplierForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });
        });
    </script>
@endsection
