/**
 * Dashboard Home Page Script (jQuery)
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
                    window.Toast.show('File deleted successfully', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.Toast.show(response.message || 'Failed to delete file', 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to delete file', 'error');
            }
        });
    };
});