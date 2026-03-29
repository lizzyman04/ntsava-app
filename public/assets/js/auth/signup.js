/**
 * Signup Page Script
 */

$(document).ready(function() {
    var usernameRegex = /^[a-zA-Z0-9_-]+$/;

    // Password strength meter
    function checkPasswordStrength(password) {
        var strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        var percentage = (strength / 4) * 100;
        var color = '';
        var text = '';
        
        if (strength === 0) {
            color = '#e5e7eb';
            text = 'Very weak';
        } else if (strength === 1) {
            color = '#ef4444';
            text = 'Weak';
        } else if (strength === 2) {
            color = '#f59e0b';
            text = 'Fair';
        } else if (strength === 3) {
            color = '#10b981';
            text = 'Good';
        } else {
            color = '#f59e0b';
            text = 'Strong';
        }
        
        $('#strengthBar').css({
            'width': percentage + '%',
            'background-color': color
        });
        
        $('#strengthText').html('<i class="fas fa-shield-alt mr-1"></i> Password strength: ' + text);
        
        if (strength > 0) {
            $('#strengthText').css('color', color);
        } else {
            $('#strengthText').css('color', '#6b7280');
        }
    }

    // Password visibility toggle
    $('#togglePassword').on('click', function() {
        var passwordField = $('#password');
        var type = passwordField.attr('type') === 'password' ? 'text' : 'password';
        passwordField.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });
    
    // Password strength on input
    $('#password').on('input', function() {
        checkPasswordStrength($(this).val());
    });
    
    // Username validation
    $('#username').on('input', function() {
        var username = $(this).val();
        if (username && !usernameRegex.test(username)) {
            $(this).addClass('border-red-500');
            $('#usernameError').show();
        } else {
            $(this).removeClass('border-red-500');
            $('#usernameError').hide();
        }
    });
    
    // Form submission
    $('#signupForm').on('submit', function(e) {
        e.preventDefault();
        
        var username = $('#username').val();
        
        if (!usernameRegex.test(username)) {
            window.Toast.show('Username can only contain letters, numbers, underscore and hyphen', 'error');
            return false;
        }
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '/auth/signup',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.Toast.show(response.message, 'success');
                    setTimeout(function() {
                        if (response.data && response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            window.location.href = '/auth/login';
                        }
                    }, 1000);
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.details) {
                    var errorMsg = Object.values(response.details).join('\n');
                    window.Toast.show('Validation failed: ' + errorMsg, 'error');
                } else if (response) {
                    window.Toast.show(response.message, 'error');
                } else {
                    window.Toast.show('An error occurred. Please try again.', 'error');
                }
            }
        });
    });
});