/**
 * Credits Page Script (jQuery)
 */

$(document).ready(function () {
    window.upgradePlan = function (planSlug) {
        var confirmMessage = 'Upgrade to ' + planSlug.toUpperCase() + ' plan? This will use your credits.';
        
        if (!confirm(confirmMessage)) {
            return;
        }

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/dashboard/credits/upgrade/' + planSlug,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.success) {
                    var message = response.data.is_downgrade ? 'Downgraded' : 'Upgraded';
                    window.Toast.show(message + ' to ' + response.data.plan + ' successfully!', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to change plan', 'error');
            }
        });
    };
});