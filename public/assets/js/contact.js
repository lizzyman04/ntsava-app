/**
 * Contact Page Script
 */

$(document).ready(function() {
    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var originalText = $submitBtn.html();
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Sending...');
        
        $.ajax({
            url: '/contact',
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $form[0].reset();
                    window.Toast.show('Message sent successfully! We\'ll get back to you soon.', 'success');
                } else {
                    window.Toast.show(response.message || 'Failed to send message', 'error');
                }
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'An error occurred. Please try again.', 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});