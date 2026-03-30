function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
        window.Toast.show('Copied to clipboard!', 'success');
    }, function () {
        window.Toast.show('Failed to copy', 'error');
    });
}

$(document).ready(function () {
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                window.Toast.show(response.message, 'success');
            },
            error: function (xhr) {
                window.Toast.show(xhr.responseJSON?.message || 'Failed to update profile', 'error');
            }
        });
    });

    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();
        var newPass = $('input[name="new_password"]').val();
        var confirmPass = $('input[name="confirm_password"]').val();

        if (newPass !== confirmPass) {
            window.Toast.show('Passwords do not match', 'error');
            return;
        }

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                window.Toast.show(response.message, 'success');
                $('#passwordForm')[0].reset();
            },
            error: function (xhr) {
                window.Toast.show(xhr.responseJSON?.message || 'Failed to update password', 'error');
            }
        });
    });

    $('#deleteAccountBtn').on('click', function () {
        if (confirm('Are you absolutely sure? This action cannot be undone. All your files will be permanently deleted.')) {
            $.ajax({
                url: $('#deleteAccountForm').attr('action'),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    window.Toast.show('Account deleted. Redirecting...', 'success');
                    setTimeout(function () {
                        window.location.href = '/';
                    }, 2000);
                },
                error: function (xhr) {
                    window.Toast.show(xhr.responseJSON?.message || 'Failed to delete account', 'error');
                }
            });
        }
    });
});