<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    if (!window.appDialogsInitialized) {
        window.appDialogsInitialized = true;

        window.appAlert = function(message, icon = 'warning', title = null) {
            return Swal.fire({
                title: title || (icon === 'error' ? 'Error' : icon === 'success' ? 'Success' : 'Notice'),
                text: message,
                icon: icon,
                confirmButtonText: 'OK',
                confirmButtonColor: '#0f766e'
            });
        };

        window.appConfirm = function(message, options = {}) {
            return Swal.fire({
                title: options.title || 'Are you sure?',
                text: message,
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonText: options.confirmButtonText || 'Yes, continue',
                cancelButtonText: options.cancelButtonText || 'Cancel',
                confirmButtonColor: options.confirmButtonColor || '#dc2626',
                cancelButtonColor: '#64748b',
                reverseButtons: true
            }).then(function(result) {
                return result.isConfirmed;
            });
        };

        window.appPrompt = function(message, options = {}) {
            return Swal.fire({
                title: options.title || message,
                input: 'text',
                inputPlaceholder: options.placeholder || '',
                inputValidator: function(value) {
                    if (!value || !value.trim()) {
                        return options.requiredMessage || 'Name is required';
                    }
                },
                showCancelButton: true,
                confirmButtonText: options.confirmButtonText || 'Save',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#64748b'
            }).then(function(result) {
                return result.isConfirmed ? result.value.trim() : null;
            });
        };

        document.addEventListener('submit', function(event) {
            const form = event.target.closest('form[data-confirm]');
            if (!form || form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            window.appConfirm(form.dataset.confirm, {
                confirmButtonText: form.dataset.confirmButton || 'Yes, continue'
            }).then(function(confirmed) {
                if (!confirmed) {
                    return;
                }

                form.dataset.confirmed = 'true';
                HTMLFormElement.prototype.submit.call(form);
            });
        });
    }
</script>
