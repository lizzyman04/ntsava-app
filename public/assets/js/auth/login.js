/**
 * Login Page Script (jQuery)
 */

$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var formData = $form.serialize();

        $.ajax({
            url: '/auth/login',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (response.data && response.data.redirect) {
                        window.location.href = response.data.redirect;
                    } else {
                        window.location.href = '/dashboard';
                    }
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                if (response && response.details) {
                    alert(response.message + '\n' + Object.values(response.details).join('\n'));
                } else if (response) {
                    alert(response.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});