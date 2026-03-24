$('#createTokenForm').on('submit', function (e) {
    e.preventDefault();

    var name = $('#tokenName').val();
    var permissions = [];

    $('input[type="checkbox"]:checked').each(function () {
        permissions.push($(this).val());
    });

    if (!name) {
        window.Toast.show('Token name is required', 'error');
        return;
    }

    $.ajax({
        url: '/dashboard/tokens/create',
        method: 'POST',
        data: JSON.stringify({
            name: name,
            permissions: permissions
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                alert('Token created: ' + response.data.token + '\nSave this token now. It won\'t be shown again.');
                location.reload();
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