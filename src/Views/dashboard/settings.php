<?php
// Load helpers
require_once __DIR__ . '/../components/helpers.php';

/**
 * @var \Source\Models\User $user
 * @var array $userData
 * @var string $title
 * @var string $page_title
 * @var string $active_menu
 */
use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
Settings
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
settings
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .settings-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .settings-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: rgba(0, 0, 0, 0.02);
    }
    
    .settings-body {
        padding: 1.5rem;
    }
    
    .api-key-display {
        font-family: monospace;
        background: var(--gray-100);
        padding: 0.75rem;
        border-radius: 0.5rem;
        word-break: break-all;
    }
    
    .dark .api-key-display {
        background: var(--gray-800);
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Account Settings</h1>
    
    <!-- Profile Settings -->
    <div class="settings-card mb-6">
        <div class="settings-header">
            <h2 class="text-lg font-semibold">Profile Information</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Update your account information</p>
        </div>
        <div class="settings-body">
            <form id="profileForm" action="/dashboard/settings/profile" method="POST">
                <?= csrf_field() ?>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input type="text" name="name" class="input-field" value="<?= htmlspecialchars($user->getName()) ?>" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                        <input type="text" name="username" class="input-field" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
                        <p class="text-xs text-gray-500 mt-1">Only letters, numbers, underscore and hyphen</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" class="input-field" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                </div>
                <button type="submit" class="btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
    
    <!-- Security Settings -->
    <div class="settings-card mb-6">
        <div class="settings-header">
            <h2 class="text-lg font-semibold">Security</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Change your password</p>
        </div>
        <div class="settings-body">
            <form id="passwordForm" action="/dashboard/settings/password" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="input-field" required>
                </div>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="new_password" class="input-field" required minlength="6">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input type="password" name="confirm_password" class="input-field" required minlength="6">
                    </div>
                </div>
                <button type="submit" class="btn-primary">Update Password</button>
            </form>
        </div>
    </div>
    
    <!-- API Settings -->
    <div class="settings-card mb-6">
        <div class="settings-header">
            <h2 class="text-lg font-semibold">API Credentials</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Your API authentication credentials</p>
        </div>
        <div class="settings-body">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your User UUID</label>
                <div class="api-key-display"><?= $user->getUuid() ?></div>
                <button onclick="copyToClipboard('<?= $user->getUuid() ?>')" class="text-sm text-primary-500 mt-2">
                    <i class="fas fa-copy mr-1"></i> Copy UUID
                </button>
            </div>
            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div class="flex gap-2">
                    <i class="fas fa-info-circle text-yellow-500"></i>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Keep your UUID secure. Never share it publicly. Use API tokens for application access.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Plan & Billing -->
    <div class="settings-card mb-6">
        <div class="settings-header">
            <h2 class="text-lg font-semibold">Current Plan</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage your subscription</p>
        </div>
        <div class="settings-body">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Your Plan</p>
                    <p class="text-xl font-bold">Free</p>
                </div>
                <a href="/dashboard/credits" class="btn-outline text-sm">Upgrade Plan</a>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Storage Used</p>
                    <p class="font-semibold"><?= round($user->getStorageUsedBytes() / 1073741824, 2) ?> / <?= round($user->getStorageLimitBytes() / 1073741824, 2) ?> GB</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bandwidth Used</p>
                    <p class="font-semibold"><?= round($user->getBandwidthUsedBytes() / 1073741824, 2) ?> / <?= round($user->getBandwidthLimitBytes() / 1073741824, 2) ?> GB</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Danger Zone -->
    <div class="settings-card border-red-500 border">
        <div class="settings-header bg-red-50 dark:bg-red-900/20">
            <h2 class="text-lg font-semibold text-red-600">Danger Zone</h2>
            <p class="text-sm text-red-500">Irreversible actions</p>
        </div>
        <div class="settings-body">
            <div class="flex justify-between items-center">
                <div>
                    <p class="font-medium">Delete Account</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Permanently delete your account and all associated data</p>
                </div>
                <form id="deleteAccountForm" action="/dashboard/settings/delete" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" id="deleteAccountBtn" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        window.Toast.show('Copied to clipboard!', 'success');
    }, function() {
        window.Toast.show('Failed to copy', 'error');
    });
}

$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            window.Toast.show(response.message, 'success');
        },
        error: function(xhr) {
            window.Toast.show(xhr.responseJSON?.message || 'Failed to update profile', 'error');
        }
    });
});

$('#passwordForm').on('submit', function(e) {
    e.preventDefault();
    var newPass = $('input[name="new_password"]').val();
    var confirmPass = $('input[name="confirm_password"]').val();
    
    if (newPass !== confirmPass) {
        window.Toast.show('Passwords do not match', 'error');
        return;
    }
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            window.Toast.show(response.message, 'success');
            $('#passwordForm')[0].reset();
        },
        error: function(xhr) {
            window.Toast.show(xhr.responseJSON?.message || 'Failed to update password', 'error');
        }
    });
});

$('#deleteAccountBtn').on('click', function() {
    if (confirm('Are you absolutely sure? This action cannot be undone. All your files will be permanently deleted.')) {
        $.ajax({
            url: $('#deleteAccountForm').attr('action'),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                window.Toast.show('Account deleted. Redirecting...', 'success');
                setTimeout(function() {
                    window.location.href = '/';
                }, 2000);
            },
            error: function(xhr) {
                window.Toast.show(xhr.responseJSON?.message || 'Failed to delete account', 'error');
            }
        });
    }
});
</script>
<?php View::endSection(); ?>