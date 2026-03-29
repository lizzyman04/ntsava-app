/**
 * Tokens Page Script (jQuery)
 */

$(document).ready(function () {
    $('#createTokenForm').on('submit', function (e) {
        e.preventDefault();

        var name = $('#tokenName').val();
        var permissions = [];

        $('input[name="permissions[]"]:checked').each(function () {
            permissions.push($(this).val());
        });

        if (!name) {
            window.Toast.show('Token name is required', 'error');
            return;
        }

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/dashboard/tokens/create',
            method: 'POST',
            data: JSON.stringify({
                name: name,
                permissions: permissions
            }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.success) {
                    var tokenMsg = 'Token created: ' + response.data.token + '\n\nSave this token now. It won\'t be shown again.';
                    alert(tokenMsg);
                    window.location.reload();
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to create token', 'error');
            }
        });
    });

    window.revokeToken = function (tokenId) {
        if (!confirm('Are you sure you want to revoke this token?')) {
            return;
        }

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: '/dashboard/tokens/' + tokenId,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                if (response.success) {
                    window.Toast.show('Token revoked successfully', 'success');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to revoke token', 'error');
            }
        });
    };
});