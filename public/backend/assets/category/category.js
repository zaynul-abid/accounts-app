$(document).ready(function () {
    console.log("✅ JS is working and jQuery is ready!");

    // Handle modal show event (for both create and edit)
    $('#categoryModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget); // Button that triggered the modal
        const action = button.data('action'); // Extract action (create or edit)

        // Reset form and messages
        $('#categoryForm')[0].reset();
        $('#formError').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');

        if (action === 'edit') {
            // Edit action - populate form with category data
            $('#modalTitle').text('Edit Category');
            $('#formMethod').val('PUT');

            const categoryId = button.data('id');
            $('#categoryId').val(categoryId);
            $('#nameField').val(button.data('name'));
            $('#descriptionField').val(button.data('description'));
            $('#statusField').val(button.data('status'));
        } else {
            // Create action
            $('#modalTitle').text('Add New Category');
            $('#formMethod').val('POST');
            $('#categoryId').val('');
        }
    });

    // Prevent form submission on Enter key and handle focus movement
    $('#categoryForm').on('keydown', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const inputs = $(this).find('input, textarea, select');
            const currentIndex = inputs.index(document.activeElement);
            if (currentIndex >= 0 && currentIndex < inputs.length - 1) {
                inputs.eq(currentIndex + 1).focus();
            }
            return false;
        }
    });

    // Handle form submission
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const method = $('#formMethod').val();
        const categoryId = $('#categoryId').val();

        // Clear any previous messages and validation classes
        $('#formError').addClass('d-none').text('');
        $('#categoryForm input, #categoryForm select, #categoryForm textarea').removeClass('is-invalid');

        // Determine the URL based on action
        let url = "{{ route('categories.store') }}";
        if (method === 'PUT') {
            url = "{{ route('categories.update', '') }}/" + categoryId;
        }

        $.ajax({
            url: url,
            method: method === 'PUT' ? 'POST' : 'POST', // Laravel needs POST for PUT with _method
            data: formData,
            success: function (response) {
                // Show success popup
                Swal.fire({
                    title: method === 'PUT' ? 'Updated!' : 'Created!',
                    text: response.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('#categoryModal').modal('hide');
                    location.reload(); // Refresh to show changes
                });
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    // Laravel validation errors
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<ul class="mb-0">';

                    $.each(errors, function(field, messages) {
                        // Add error class to the field
                        $('#' + field + 'Field').addClass('is-invalid');

                        // Add error messages
                        $.each(messages, function(index, message) {
                            errorHtml += '<li>' + message + '</li>';
                        });
                    });

                    errorHtml += '</ul>';
                    $('#formError').removeClass('d-none').html(errorHtml);
                } else {
                    // Other error (server issue, etc.)
                    $('#formError').removeClass('d-none')
                        .html('Something went wrong. Please try again.<br>Error: ' +
                            (xhr.responseJSON.message || xhr.statusText));
                }
            }
        });
    });

    // Clear form when modal is closed
    $('#categoryModal').on('hidden.bs.modal', function () {
        $('#categoryForm')[0].reset();
        $('#formError').addClass('d-none').text('');
        $('.is-invalid').removeClass('is-invalid');
    });

    // Delete category handler
    $(document).on('click', '.delete-category', function() {
        const categoryId = $(this).data('id');
        const categoryStatus = $(this).data('status');
        const categoryName = $(this).data('name');

        if (categoryStatus == 1) {
            // Category is active - show warning
            Swal.fire({
                title: 'Cannot Delete Active Category',
                text: `The category "${categoryName}" is currently active. Please deactivate it first before deleting.`,
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // If inactive, confirm deletion
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the category "${categoryName}". This action cannot be undone.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion
                $.ajax({
                    url: "{{ route('categories.destroy', '') }}/" + categoryId,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Remove the row from table without reload
                            $(`button.delete-category[data-id="${categoryId}"]`)
                                .closest('tr').fadeOut(300, function() {
                                $(this).remove();
                                // If no rows left, show empty message
                                if ($('tbody tr').length === 0) {
                                    $('tbody').html(
                                        '<tr><td colspan="5" class="text-center">No categories found.</td></tr>'
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

$(document).on('click', '.delete-category', function() {
    const categoryId = $(this).data('id');
    const categoryStatus = $(this).data('status');
    const categoryName = $(this).data('name');

    if (categoryStatus == 1) {
        // Category is active - show warning
        Swal.fire({
            title: 'Cannot Delete Active Category',
            text: `The category "${categoryName}" is currently active. Please deactivate it first before deleting.`,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // If inactive, confirm deletion
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete the category "${categoryName}". This action cannot be undone.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with deletion
            $.ajax({
                url: "{{ route('categories.destroy', '') }}/" + categoryId,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        // Remove the row from table without reload
                        $(`button.delete-category[data-id="${categoryId}"]`)
                            .closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            // If no rows left, show empty message
                            if ($('tbody tr').length === 0) {
                                $('tbody').html(
                                    '<tr><td colspan="5" class="text-center">No categories found.</td></tr>'
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
