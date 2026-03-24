<?php
// Load helpers
require_once __DIR__ . '/../components/helpers.php';

/**
 * @var \Source\Models\User $user
 * @var \Source\Models\File[] $files
 * @var string $title
 * @var string $page_title
 * @var string $active_menu
 */
use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
My Files
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
files
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Files</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage all your uploaded files</p>
        </div>
        <a href="/dashboard/upload" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Upload New
        </a>
    </div>

    <?php if (empty($files)): ?>
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="fas fa-folder-open text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No files yet</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Start uploading your first file to the CDN</p>
            <a href="/dashboard/upload" class="btn-primary">Upload File</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">File</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Size</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Uploaded</th>
                        <th class="text-left py-3 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <?php /** @var \Source\Models\File $file */ ?>
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <i class="fas <?= $file->isImage() ? 'fa-image text-primary-500' : 'fa-file text-gray-500' ?>"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($file->getOriginalName()) ?>
                                        </p>
                                        <p class="text-xs text-gray-500"><?= substr($file->getUuid(), 0, 8) ?>...</p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-gray-600 dark:text-gray-400"><?= format_file_size($file->getSizeBytes()) ?></td>
                            <td class="text-gray-600 dark:text-gray-400"><?= $file->getCreatedAt()->format('d/m/Y H:i') ?></td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <a href="<?= $user->getPublicUrl($file->getStoragePath()) ?>" target="_blank"
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition" title="View">
                                        <i class="fas fa-eye text-gray-500 hover:text-primary-500"></i>
                                    </a>
                                    <button onclick="deleteFile('<?= $file->getUuid() ?>')"
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
                                        title="Delete">
                                        <i class="fas fa-trash text-gray-500 hover:text-red-500"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="<?= asset('js/dashboard/files.js') ?>"></script>
<?php View::endSection(); ?>