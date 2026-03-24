<?php
// Load helpers
require_once __DIR__ . '/../components/helpers.php';

/**
 * @var \Source\Models\User $user
 * @var \Source\Models\File[] $recent_files
 * @var int $total_files
 * @var float $storage_used_gb
 * @var float $storage_limit_gb
 * @var float $storage_percent
 * @var float $bandwidth_used_gb
 * @var float $bandwidth_limit_gb
 * @var float $bandwidth_percent
 * @var float $credits
 * @var string $title
 * @var string $page_title
 * @var string $active_menu
 */
use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
Dashboard
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
dashboard
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<!-- Welcome Section -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome back,
        <?= htmlspecialchars($user->getName()) ?>! 👋
    </h1>
    <p class="text-gray-500 dark:text-gray-400 mt-2">Here's what's happening with your CDN today.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php component('stat-card', [
        'icon' => 'fa-database',
        'label' => 'Storage Used',
        'value' => number_format($storage_used_gb, 2) . ' GB',
        'subtext' => 'of ' . number_format($storage_limit_gb, 2) . ' GB',
        'percent' => $storage_percent
    ]); ?>

    <?php component('stat-card', [
        'icon' => 'fa-chart-line',
        'label' => 'Bandwidth Used',
        'value' => number_format($bandwidth_used_gb, 2) . ' GB',
        'subtext' => 'of ' . number_format($bandwidth_limit_gb, 2) . ' GB',
        'percent' => $bandwidth_percent
    ]); ?>

    <?php component('stat-card', [
        'icon' => 'fa-file-alt',
        'label' => 'Total Files',
        'value' => number_format($total_files),
        'subtext' => 'Uploaded files'
    ]); ?>

    <?php component('stat-card', [
        'icon' => 'fa-coins',
        'label' => 'Credits',
        'value' => number_format($credits, 2),
        'subtext' => 'Available credits'  // Removido $user->getCurrency()
    ]); ?>
</div>

<!-- Recent Files -->
<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Files</h2>
        <a href="/dashboard/files" class="text-primary-500 hover:text-primary-600 transition flex items-center gap-1">
            View All
            <i class="fas fa-arrow-right text-sm"></i>
        </a>
    </div>

    <?php if (empty($recent_files)): ?>
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No files uploaded yet.</p>
            <a href="/dashboard/upload" class="btn-primary inline-block mt-4">Upload Your First File</a>
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

                </thead>
                <tbody>
                    <?php foreach ($recent_files as $file): ?>
                        <?php /** @var \Source\Models\File $file */ ?>
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <i
                                            class="fas <?= $file->isImage() ? 'fa-image text-primary-500' : 'fa-file text-gray-500' ?>"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white truncate max-w-xs">
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

<script src="<?= asset('js/dashboard/index.js') ?>"></script>
<?php View::endSection(); ?>