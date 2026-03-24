<?php
/**
 * Sidebar Component
 * 
 * @var array|null $user Authenticated user data
 * @var int $total_files Total number of user files
 * @var float $credits User credits balance
 * @var string $active_menu Current active menu item
 * @var bool $is_admin Whether user is admin
 */
?>
<aside
    class="sidebar fixed left-0 top-0 h-full w-72 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40">
    <div class="p-6">
        <div class="flex items-center space-x-3 mb-8">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                <i class="fas fa-cloud-upload-alt text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">CDN App</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @<?= htmlspecialchars($user['username'] ?? 'user') ?></p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="/dashboard" class="nav-link <?= $active_menu === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="/dashboard/files" class="nav-link <?= $active_menu === 'files' ? 'active' : '' ?>">
                <i class="fas fa-folder-open w-5"></i>
                <span>My Files</span>
                <span class="ml-auto text-xs text-gray-500"><?= $total_files ?></span>
            </a>
            <a href="/dashboard/upload" class="nav-link <?= $active_menu === 'upload' ? 'active' : '' ?>">
                <i class="fas fa-cloud-upload-alt w-5"></i>
                <span>Upload</span>
            </a>
            <a href="/dashboard/tokens" class="nav-link <?= $active_menu === 'tokens' ? 'active' : '' ?>">
                <i class="fas fa-key w-5"></i>
                <span>API Tokens</span>
            </a>
            <a href="/dashboard/credits" class="nav-link <?= $active_menu === 'credits' ? 'active' : '' ?>">
                <i class="fas fa-coins w-5"></i>
                <span>Credits</span>
                <span class="ml-auto text-xs font-semibold text-primary-500"><?= number_format($credits, 2) ?></span>
            </a>
        </nav>

        <div class="absolute bottom-6 left-0 right-0 px-6">
            <a href="/auth/logout" class="nav-link text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<style>
    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        color: #6b7280;
        transition: all 0.2s;
    }

    .dark .nav-link {
        color: #9ca3af;
    }

    .nav-link:hover {
        background-color: #f3f4f6;
        color: #111827;
    }

    .dark .nav-link:hover {
        background-color: #1f2937;
        color: white;
    }

    .nav-link.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
</style>