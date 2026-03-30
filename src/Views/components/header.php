<?php
/**
 * Header Component
 * 
 * @var array|null $user Authenticated user data
 */
?>
<header class="sticky top-0 z-50 bg-white shadow-md border-b border-gray-200">
    <nav class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo e Menu Mobile -->
            <div class="flex items-center space-x-6">
                <button id="mobile-menu-btn" 
                    class="lg:hidden text-gray-600 hover:text-primary-600 transition-transform duration-200 hover:scale-110"
                    aria-label="Toggle menu">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <a href="<?= \App\Core\Auth::check() ? '/dashboard' : '/' ?>" 
                    class="text-2xl font-bold text-primary-600 hover:text-primary-700 transition-colors">
                    Ntsava
                </a>
                <?php if (!\App\Core\Auth::check()): ?>
                <div class="hidden lg:flex space-x-6">
                    <a href="/about" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">About</a>
                    <a href="/docs" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">API Docs</a>
                    <a href="/terms" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">Terms of Service</a>
                    <a href="/privacy" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">Privacy Policy</a>
                    <a href="/contact" class="text-gray-600 hover:text-primary-600 transition-colors duration-200">Contact</a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Ações do Usuário -->
            <div class="flex items-center space-x-4">
                <?php if (\App\Core\Auth::check()): ?>
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-btn" 
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-all duration-200"
                            aria-label="User menu"
                            aria-expanded="false">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-gray-800 text-sm font-bold shadow-md">
                                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="hidden md:inline text-gray-700 font-medium">
                                <?= htmlspecialchars($user['name'] ?? 'User') ?>
                            </span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"></i>
                        </button>
                        
                        <div id="user-dropdown" 
                            class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg opacity-0 invisible transition-all duration-200 z-50 border border-gray-200">
                            <div class="py-2">
                                <div class="px-4 py-3 border-b border-gray-200">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['name'] ?? 'User') ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                                </div>
                                <a href="/dashboard" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-tachometer-alt w-5 mr-3 text-gray-400"></i>
                                    Dashboard
                                </a>
                                <a href="/dashboard/settings" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                    Settings
                                </a>
                                <hr class="my-1 border-gray-200">
                                <a href="/auth/logout" 
                                    class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5 mr-3 text-red-500"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/auth/login" 
                        class="text-gray-600 hover:text-primary-600 transition-colors duration-200">Login</a>
                    <a href="/auth/signup" 
                        class="btn-primary text-sm px-4 py-2">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 opacity-0 invisible transition-all duration-300 z-40"></div>