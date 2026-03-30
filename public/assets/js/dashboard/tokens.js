/**
 * Tokens Page Script (jQuery)
 */

$(document).ready(function () {
    function showTokenModal(token, name) {
        $('.token-modal').remove();

        var modalHtml = `
            <div class="token-modal fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeTokenModal()"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-3xl text-green-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Token Created Successfully!</h3>
                        <p class="text-gray-600 mt-2">Copy your token below. It will not be shown again.</p>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token Name</label>
                        <input type="text" value="${escapeHtml(name)}" readonly class="w-full px-4 py-2 bg-gray-100 rounded-lg border border-gray-300">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Token Value</label>
                        <div class="flex gap-2">
                            <input type="text" id="tokenValue" value="${escapeHtml(token)}" readonly class="flex-1 px-4 py-2 bg-gray-100 rounded-lg border border-gray-300 font-mono text-sm">
                            <button onclick="copyToken()" class="px-4 py-2 bg-primary-500 text-gray-800 rounded-lg hover:bg-primary-600 transition">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Make sure to copy your token now. You won't be able to see it again!
                        </p>
                    </div>
                    
                    <button onclick="closeTokenModal()" class="w-full btn-primary">
                        I've Copied the Token
                    </button>
                </div>
            </div>
        `;

        $('body').append(modalHtml);
        $('body').css('overflow', 'hidden');

        // Select token text for easy copying
        setTimeout(function () {
            var tokenInput = document.getElementById('tokenValue');
            if (tokenInput) {
                tokenInput.select();
            }
        }, 100);
    }

    window.closeTokenModal = function () {
        $('.token-modal').remove();
        $('body').css('overflow', '');
        window.location.reload();
    };

    window.copyToken = function () {
        var tokenInput = document.getElementById('tokenValue');
        if (tokenInput) {
            tokenInput.select();
            document.execCommand('copy');
            window.Toast.show('Token copied to clipboard!', 'success');
        }
    };

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function (m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

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
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalText = $submitBtn.html();

        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Creating...');

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
                    // Show modal with token
                    showTokenModal(response.data.token, response.data.name);
                    // Clear form
                    $('#tokenName').val('');
                    $('input[name="permissions[]"]').prop('checked', true);
                } else {
                    window.Toast.show(response.message, 'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                window.Toast.show(response?.message || 'Failed to create token', 'error');
            },
            complete: function () {
                $submitBtn.prop('disabled', false).html(originalText);
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