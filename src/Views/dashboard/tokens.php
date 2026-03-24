<?php
// Load helpers
require_once __DIR__ . '/../components/helpers.php';

/**
 * @var \Source\Models\User $user
 * @var \Source\Models\ApiToken[] $tokens
 * @var string $title
 * @var string $page_title
 * @var string $active_menu
 */
use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
API Tokens
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
tokens
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="glass-card p-6 mb-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Create New Token</h2>
    <form id="createTokenForm">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Token Name</label>
            <input type="text" id="tokenName" class="input-field" placeholder="My API Key" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
            <div class="flex flex-wrap gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" value="upload" checked> <span>Upload</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" value="delete" checked> <span>Delete</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" value="read" checked> <span>Read</span>
                </label>
            </div>
        </div>
        <button type="submit" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Create Token
        </button>
    </form>
</div>

<div class="glass-card p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Your Tokens</h2>

    <?php if (empty($tokens)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="fas fa-key text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No API tokens created yet.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Name</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Permissions</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Last Used</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Created</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $token): ?>
                        <?php /** @var \Source\Models\ApiToken $token */ ?>
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="py-3 font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($token->getName()) ?></td>
                            <td class="text-gray-600 dark:text-gray-400">
                                <?php foreach ($token->getPermissions() as $perm): ?>
                                    <span
                                        class="inline-block px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded mr-1"><?= $perm ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td class="text-gray-600 dark:text-gray-400">
                                <?= $token->getLastUsedAt() ? $token->getLastUsedAt()->format('d/m/Y H:i') : 'Never' ?></td>
                            <td class="text-gray-600 dark:text-gray-400"><?= $token->getCreatedAt()->format('d/m/Y H:i') ?></td>
                            <td class="py-3">
                                <button onclick="revokeToken(<?= $token->getId() ?>)"
                                    class="text-red-500 hover:text-red-700 transition" title="Revoke Token">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="<?= asset('js/dashboard/tokens.js') ?>"></script>
<?php View::endSection(); ?>