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
<div class="stat-card bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1"><?= htmlspecialchars($label) ?></p>
            <p class="text-3xl font-bold text-gray-900"><?= $value ?></p>
            <?php if (isset($subtext)): ?>
                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($subtext) ?></p>
            <?php endif; ?>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
            <i class="fas <?= htmlspecialchars($icon) ?> text-2xl text-primary-600"></i>
        </div>
    </div>
    <?php if (isset($percent)): ?>
        <div class="mt-4">
            <div class="progress-bar bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="progress-fill bg-gradient-to-r from-primary-500 to-primary-600 h-full rounded-full transition-all duration-1000" style="width: <?= (float) $percent ?>%"></div>
            </div>
        </div>
    <?php endif; ?>
</div>