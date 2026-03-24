/**
 * Credits Page Script (jQuery)
 */

$(document).ready(function () {
    window.upgradePlan = function (planSlug) {
        if (!confirm('Upgrade to ' + planSlug.toUpperCase() + ' plan?')) {
            return;
        }

        $.ajax({
            url: '/dashboard/credits/upgrade/' + planSlug,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    window.Toast.show('Plan upgraded successfully!', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500);
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to upgrade plan', 'error');
            }
        });
    };
});