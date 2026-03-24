/**
 * Signup Page Script (jQuery)
 */

$(document).ready(function () {
    var usernameRegex = /^[a-zA-Z0-9_-]+$/;

    $('#signupForm').on('submit', function (e) {
        e.preventDefault();

        var username = $('input[name="username"]').val();

        if (!usernameRegex.test(username)) {
            alert('Username can only contain letters, numbers, underscore and hyphen');
            return false;
        }

        var formData = $(this).serialize();

        $.ajax({
            url: '/auth/signup',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    if (response.data && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        window.location.href = '/auth/login';
                    }
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                if (response && response.details) {
                    var errorMsg = Object.values(response.details).join('\n');
                    alert('Validation failed:\n' + errorMsg);
                } else if (response) {
                    alert(response.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});