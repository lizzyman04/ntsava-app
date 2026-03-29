/**
 * Login Page Script
 */

$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var formData = $form.serialize();

        $.ajax({
            url: '/auth/login',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.Toast.show('Logged in successfully!', 'success');
                    setTimeout(function() {
                        if (response.data && response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.href = '/dashboard';
                        }
                    }, 500);
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.details) {
                    var errorMsg = response.message + '\n' + Object.values(response.details).join('\n');
                    window.Toast.show(errorMsg, 'error');
                } else if (response) {
                    window.Toast.show(response.message, 'error');
                } else {
                    window.Toast.show('An error occurred. Please try again.', 'error');
                }
            }
        });
    });
});