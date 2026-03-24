/**
 * Dashboard Home Page Script (jQuery)
 */

$(document).ready(function () {
    window.deleteFile = function (uuid) {
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

        $.ajax({
            url: '/api/v1/delete',
            method: 'DELETE',
            headers: {
                'X-User-UUID': $('#user-uuid').data('uuid'),
                'X-Token': $('#api-token').data('token')
            },
            data: { uuid: uuid },
            success: function () {
                window.location.reload();
            },
            error: function () {
                window.Toast.show('Failed to delete file', 'error');
            }
        });
    };
});