<?php
/**
 * Stat Card Component
 * 
 * @var string $icon Font Awesome icon class
 * @var string $label Card label
 * @var string $value Main value to display
 * @var string|null $subtext Optional subtext
 * @var float|null $percent Progress percentage (0-100)
 */
?>
<div class="stat-card group">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1"><?= htmlspecialchars($label) ?></p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white"><?= $value ?></p>
            <?php if (isset($subtext)): ?>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?= htmlspecialchars($subtext) ?></p>
            <?php endif; ?>
        </div>
        <div
            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 flex items-center justify-center group-hover:scale-110 transition">
            <i class="fas <?= htmlspecialchars($icon) ?> text-2xl text-primary-500"></i>
        </div>
    </div>
    <?php if (isset($percent)): ?>
        <div class="mt-4">
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= (float) $percent ?>%"></div>
            </div>
        </div>
    <?php endif; ?>
</div>