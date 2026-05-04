<?php
require_once __DIR__ . '/../components/helpers.php';

use Fluxor\View;
?>
<?php View::extend('layouts/dashboard'); ?>

<?php View::section('title'); ?>
Upload Files
<?php View::endSection(); ?>

<?php View::section('active_menu'); ?>
upload
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
    }

    .drop-zone.drag-over {
        border-color: #f59e0b;
        background-color: rgba(245, 158, 11, 0.05);
    }

    .upload-progress {
        transition: width 0.3s ease;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="glass-card p-6">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Upload Files</h1>
        <p class="text-gray-500 mt-1">Drag and drop files or click to select</p>
    </div>

    <div class="drop-zone" id="dropZone"
         data-max-size="<?= $max_size ?>"
         data-allowed-types="<?= htmlspecialchars($allowed_types) ?>">
        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-4"></i>
        <p class="text-gray-600">Drag & drop files here or click to select</p>
        <p class="text-sm text-gray-500 mt-2">
            Max size: <span class="font-medium"><?= format_file_size($max_size) ?></span>
            <?php if ($plan): ?>
                <span class="text-xs text-gray-400">(<?= htmlspecialchars($plan->getName()) ?> plan)</span>
            <?php endif; ?>
        </p>
        <div class="flex flex-wrap gap-1 justify-center mt-2">
            <?php foreach (explode(',', $allowed_types) as $type): ?>
                <span class="inline-block px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-600 font-mono">.<?= htmlspecialchars(trim($type)) ?></span>
            <?php endforeach; ?>
        </div>
        <input type="file" id="fileInput" style="display: none;" multiple
               accept="<?= implode(',', array_map(fn($t) => '.' . trim($t), explode(',', $allowed_types))) ?>">
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Path (optional)</label>
        <input type="text" id="path" class="input-field" placeholder="e.g., photos/2026/">
        <p class="text-xs text-gray-500 mt-1">Files will be stored at:
            /u/<?= $user->getUsername() ?>/[path]/filename</p>
    </div>

    <div id="uploadQueue" class="mt-6" style="display: none;">
        <h3 class="font-semibold text-gray-900 mb-3">Upload Queue</h3>
        <div id="queueList" class="space-y-2"></div>
    </div>
</div>

<script src="<?= asset('js/dashboard/upload.js') ?>"></script>
<?php View::endSection(); ?>