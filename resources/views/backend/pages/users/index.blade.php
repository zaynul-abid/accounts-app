@extends('backend.layouts.app')
@section('title', 'Users')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Users')
@section('sub-header', 'Users')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Users</h3>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#userModal" data-action="create">
                                    Add New User
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
                                        <th>Email</th>
                                        <th>User Type</th>
                                        <th>Company</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($users as $index => $user)
                                        <tr data-user-id="{{ $user->id }}">
                                            <td>{{ $users->firstItem() + $index }}.</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ ucfirst($user->usertype) }}</td>
                                            <td>{{ $user->company ? $user->company->name : 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-user"
                                                            data-toggle="modal"
                                                            data-target="#userModal"
                                                            data-action="edit"
                                                            data-id="{{ $user->id }}"
                                                            data-name="{{ $user->name }}"
                                                            data-email="{{ $user->email }}"
                                                            data-usertype="{{ $user->usertype }}"
                                                            data-company-id="{{ $user->company_id }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-user"
                                                            data-id="{{ $user->id }}"
                                                            data-name="{{ $user->name }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No users found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($users->hasPages())
                            <div class="card-footer clearfix">
                                {{ $users->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- User Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="user_id" id="userId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New User</h5>
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

                        <!-- Email -->
                        <div class="form-group">
                            <label for="emailField">Email</label>
                            <input type="email" name="email" class="form-control" id="emailField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="passwordField">Password</label>
                            <input type="password" name="password" class="form-control" id="passwordField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="form-group">
                            <label for="passwordConfirmationField">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="passwordConfirmationField" />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- User Type -->
                        <div class="form-group">
                            <label for="usertypeField">User Type</label>
                            <select name="usertype" class="form-control" id="usertypeField" required>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                                <option value="employee">Employee</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Company -->
                        <div class="form-group">
                            <label for="companyIdField">Company</label>
                            <select name="company_id" class="form-control" id="companyIdField" required>
                                <option value="">Select a company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
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
            $('#userModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#userForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
                $('#formMethod').val('POST');
                $('#modalTitle').text('Add New User');
                $('#passwordField').prop('required', true);
                $('#passwordConfirmationField').prop('required', true);

                if (action === 'edit') {
                    $('#modalTitle').text('Edit User');
                    $('#formMethod').val('PUT');
                    $('#userId').val(button.data('id'));
                    $('#nameField').val(button.data('name'));
                    $('#emailField').val(button.data('email'));
                    $('#usertypeField').val(button.data('usertype'));
                    $('#companyIdField').val(button.data('company-id'));
                    $('#passwordField').prop('required', false);
                    $('#passwordConfirmationField').prop('required', false);
                }
            });

            // Handle user form submission
            $('#userForm').on('submit', function (e) {
                e.preventDefault();

                // Get form data
                const formData = new FormData(this);
                const submitBtn = $('#submitButton');
                const originalBtnText = submitBtn.html();
                const isEdit = $('#formMethod').val() === 'PUT';
                const userId = $('#userId').val();
                const url = isEdit ? `/users/${userId}` : '{{ route("users.store") }}';
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
                            $('#userModal').modal('hide');
                            if (isEdit) {
                                updateUserInTable(response.user);
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

            // Function to update user in the table
            function updateUserInTable(user) {
                const row = $(`tr[data-user-id="${user.id}"]`);
                row.html(`
                    <td>${row.find('td:first').text()}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.usertype.charAt(0).toUpperCase() + user.usertype.slice(1)}</td>
                    <td>${user.company_name || 'N/A'}</td>
                    <td>
                        <div class="d-flex flex-nowrap align-items-center">
                            <button class="btn btn-sm btn-warning mr-1 edit-user"
                                    data-toggle="modal"
                                    data-target="#userModal"
                                    data-action="edit"
                                    data-id="${user.id}"
                                    data-name="${user.name}"
                                    data-email="${user.email}"
                                    data-usertype="${user.usertype}"
                                    data-company-id="${user.company_id}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-user"
                                    data-id="${user.id}"
                                    data-name="${user.name}">
                                Delete
                            </button>
                        </div>
                    </td>
                `);
            }

            // Clear form when modal is closed
            $('#userModal').on('hidden.bs.modal', function () {
                $('#userForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Delete user handler with confirmation
            $(document).on('click', '.delete-user', function() {
                const userId = $(this).data('id');
                const userName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the user "${userName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/users/${userId}`,
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
                                $(`tr[data-user-id="${userId}"]`).remove();
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to delete user',
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
