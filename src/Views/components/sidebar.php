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
<aside class="sidebar fixed left-0 top-0 h-full w-72 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-40 shadow-xl">
    <div class="p-6">
        <div class="flex items-center space-x-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                <i class="fas fa-cloud-upload-alt text-white"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ntsava App</h2>
                <p class="text-sm text-gray-500">@<?= htmlspecialchars($user['username'] ?? 'user') ?></p>
            </div>
        </div>

        <nav class="space-y-1">
            <a href="/dashboard" 
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 <?= $active_menu === 'dashboard' ? 'active' : 'text-gray-600 hover:bg-gray-100' ?>">
                <i class="fas fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="/dashboard/files" 
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 <?= $active_menu === 'files' ? 'active' : 'text-gray-600 hover:bg-gray-100' ?>">
                <i class="fas fa-folder-open w-5"></i>
                <span>My Files</span>
                <span class="ml-auto text-xs text-gray-500"><?= $total_files ?></span>
            </a>
            <a href="/dashboard/upload" 
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 <?= $active_menu === 'upload' ? 'active' : 'text-gray-600 hover:bg-gray-100' ?>">
                <i class="fas fa-cloud-upload-alt w-5"></i>
                <span>Upload</span>
            </a>
            <a href="/dashboard/tokens" 
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 <?= $active_menu === 'tokens' ? 'active' : 'text-gray-600 hover:bg-gray-100' ?>">
                <i class="fas fa-key w-5"></i>
                <span>API Tokens</span>
            </a>
            <a href="/dashboard/credits" 
               class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 <?= $active_menu === 'credits' ? 'active' : 'text-gray-600 hover:bg-gray-100' ?>">
                <i class="fas fa-coins w-5"></i>
                <span>Credits</span>
                <span class="ml-auto text-xs font-semibold text-primary-600"><?= number_format($credits, 2) ?></span>
            </a>
        </nav>

        <div class="absolute bottom-6 left-0 right-0 px-6">
            <a href="/auth/logout" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</aside>

<style>
.nav-link.active {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
}

.nav-link.active i {
    color: white;
}
</style>