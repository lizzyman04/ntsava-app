/**
 * Files Page Script (jQuery)
 */

$(document).ready(function () {
    window.deleteFile = function (uuid) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        $.ajax({
            url: '/dashboard/files/' + uuid,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    window.Toast.show(response.message || 'Failed to delete file', 'error');
                }
            },
            error: function () {
                window.Toast.show('Failed to delete file', 'error');
            }
        });
    };
});