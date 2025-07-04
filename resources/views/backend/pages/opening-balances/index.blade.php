@extends('backend.layouts.app')
@section('title', 'Opening Balances')
@section('navbar')
    @include('backend.partials.sidebar.admin-sidebar')
@endsection
@section('header', 'Opening Balances')
@section('sub-header', 'Opening Balances')
@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h3 class="card-title m-0">Opening Balances</h3>
                                <div id="addNewButtonContainer">
                                    @if($canCreate)
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#openingBalanceModal" data-action="create">
                                            Add New Opening Balance
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Opening Balance</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th style="width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($openingBalances as $index => $openingBalance)
                                        <tr>
                                            <td>{{ $openingBalances->firstItem() + $index }}.</td>
                                            <td>{{ number_format($openingBalance->opening_balance, 2) }}</td>
                                            <td>{{ $openingBalance->description ?? 'N/A' }}</td>
                                            <td style="color: {{ $openingBalance->status ? 'green' : 'red' }}">
                                                {{ $openingBalance->status ? 'Active' : 'Inactive' }}
                                            </td>
                                            <td>{{ $openingBalance->createdBy->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="d-flex flex-nowrap align-items-center">
                                                    <button class="btn btn-sm btn-warning mr-1 edit-opening-balance"
                                                            data-toggle="modal"
                                                            data-target="#openingBalanceModal"
                                                            data-action="edit"
                                                            data-id="{{ $openingBalance->id }}"
                                                            data-opening-balance="{{ $openingBalance->opening_balance }}"
                                                            data-description="{{ $openingBalance->description }}"
                                                            data-status="{{ $openingBalance->status }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-opening-balance"
                                                            data-id="{{ $openingBalance->id }}"
                                                            data-opening-balance="{{ $openingBalance->opening_balance }}"
                                                            data-status="{{ $openingBalance->status }}">
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No opening balances found.</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if($openingBalances->hasPages())
                            <div class="card-footer clearfix">
                                {{ $openingBalances->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>

    <!-- Opening Balance Modal (Used for both Create and Edit) -->
    <div class="modal fade" id="openingBalanceModal" tabindex="-1" role="dialog" aria-labelledby="openingBalanceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="openingBalanceForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="opening_balance_id" id="openingBalanceId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Add New Opening Balance</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Success Message -->
                        <div id="formSuccess" class="alert alert-success d-none"></div>

                        <!-- Error Message -->
                        <div id="formError" class="alert alert-danger d-none"></div>

                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label for="openingBalanceField">Opening Balance</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" id="openingBalanceField" required />
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="descriptionField">Description</label>
                            <textarea name="description" class="form-control" id="descriptionField"></textarea>
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

            // Handle modal show event (for both create and edit)
            $('#openingBalanceModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const action = button.data('action');

                $('#openingBalanceForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');

                if (action === 'edit') {
                    $('#modalTitle').text('Edit Opening Balance');
                    $('#formMethod').val('PUT');

                    const openingBalanceId = button.data('id');
                    $('#openingBalanceId').val(openingBalanceId);
                    $('#openingBalanceField').val(button.data('opening-balance'));
                    $('#descriptionField').val(button.data('description'));
                    $('#statusField').val(button.data('status'));

                    // If status is inactive (0), clear the selection
                    if (button.data('status') == 0) {
                        $('#statusField').val('');
                    }
                } else {
                    $('#modalTitle').text('Add New Opening Balance');
                    $('#formMethod').val('POST');
                    $('#openingBalanceId').val('');
                }
            });

            // Handle form submission
            $('#openingBalanceForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);
                const method = $('#formMethod').val();
                const openingBalanceId = $('#openingBalanceId').val();

                $('#formError').addClass('d-none').text('');
                $('#openingBalanceForm input, #openingBalanceForm select, #openingBalanceForm textarea').removeClass('is-invalid');

                let url = "{{ route('opening-balances.store') }}";
                if (method === 'PUT') {
                    url = "{{ route('opening-balances.update', '') }}/" + openingBalanceId;
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
                            $('#openingBalanceModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors || {};
                            let errorMessage = xhr.responseJSON.message;
                            if (errorMessage && !Object.keys(errors).length) {
                                $('#formError').removeClass('d-none').html(errorMessage);
                            } else {
                                let errorHtml = '<ul class="mb-0">';
                                $.each(errors, function(field, messages) {
                                    $('#' + field + 'Field').addClass('is-invalid');
                                    $.each(messages, function(index, message) {
                                        errorHtml += '<li>' + message + '</li>';
                                    });
                                });
                                errorHtml += '</ul>';
                                $('#formError').removeClass('d-none').html(errorHtml);
                            }
                        } else {
                            $('#formError').removeClass('d-none')
                                .html('Something went wrong. Please try again.<br>Error: ' +
                                    (xhr.responseJSON.message || xhr.statusText));
                        }
                    }
                });
            });

            // Clear form when modal is closed
            $('#openingBalanceModal').on('hidden.bs.modal', function () {
                $('#openingBalanceForm')[0].reset();
                $('#formError').addClass('d-none').text('');
                $('.is-invalid').removeClass('is-invalid');
            });

            // Delete opening balance handler
            $(document).on('click', '.delete-opening-balance', function() {
                const openingBalanceId = $(this).data('id');
                const openingBalanceAmount = $(this).data('opening-balance');
                const openingBalanceStatus = $(this).data('status');

                if (openingBalanceStatus == 1) {
                    Swal.fire({
                        title: 'Cannot Delete Active Opening Balance',
                        text: `The opening balance of ${openingBalanceAmount} is currently active. Please deactivate it first before deleting.`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete the opening balance of ${openingBalanceAmount}. This action cannot be undone.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('opening-balances.destroy', '') }}/" + openingBalanceId,
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
                                    const $row = $(`button.delete-opening-balance[data-id="${openingBalanceId}"]`).closest('tr');
                                    $row.fadeOut(300, function() {
                                        $(this).remove();
                                        // Check if table is empty after deletion
                                        if ($('tbody tr').length === 0) {
                                            $('tbody').html(
                                                '<tr><td colspan="6" class="text-center">No opening balances found.</td></tr>'
                                            );
                                            // Show Add New button since no non-deleted records remain
                                            $('#addNewButtonContainer').html(
                                                '<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#openingBalanceModal" data-action="create">' +
                                                'Add New Opening Balance</button>'
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
