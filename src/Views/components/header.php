<?php
/**
 * Header Component
 * 
 * @var array|null $user Authenticated user data
 */
?>
<header class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800 transition-all duration-300">
    <nav class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo e Menu Mobile -->
            <div class="flex items-center space-x-6">
                <button id="mobile-menu-btn" 
                    class="lg:hidden text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-transform duration-200 hover:scale-110"
                    aria-label="Toggle menu">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <a href="/" 
                    class="text-2xl font-bold bg-gradient-to-r from-primary-500 to-primary-600 bg-clip-text text-transparent hover:opacity-80 transition-opacity">
                    CDN App
                </a>
                <div class="hidden lg:flex space-x-6">
                    <a href="/about" 
                        class="text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-colors duration-200">About</a>
                    <?php if (\App\Core\Auth::check()): ?>
                        <a href="/dashboard" 
                            class="text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-colors duration-200">Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Ações do Usuário -->
            <div class="flex items-center space-x-4">
                <!-- Theme Toggle -->
                <button id="theme-toggle" 
                    class="relative w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-primary-900 transition-all duration-200 hover:scale-110"
                    aria-label="Toggle theme">
                    <i class="fas fa-moon text-gray-600 dark:hidden text-lg"></i>
                    <i class="fas fa-sun hidden dark:inline-block text-yellow-500 text-lg"></i>
                </button>
                
                <?php if (\App\Core\Auth::check()): ?>
                    <!-- User Dropdown -->
                    <div class="relative">
                        <button id="user-menu-btn" 
                            class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 group"
                            aria-label="User menu"
                            aria-expanded="false">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white text-sm font-bold shadow-md group-hover:shadow-lg transition-shadow">
                                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <span class="hidden md:inline text-gray-700 dark:text-gray-300 font-medium">
                                <?= htmlspecialchars($user['name'] ?? 'User') ?>
                            </span>
                            <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200 group-data-[open=true]:rotate-180"></i>
                        </button>
                        
                        <div id="user-dropdown" 
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl opacity-0 invisible transition-all duration-200 z-50 border border-gray-200 dark:border-gray-700 transform -translate-y-2">
                            <div class="py-2">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($user['name'] ?? 'User') ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                                </div>
                                <a href="/dashboard" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-tachometer-alt w-5 mr-3 text-gray-400"></i>
                                    Dashboard
                                </a>
                                <a href="/dashboard/settings" 
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-cog w-5 mr-3 text-gray-400"></i>
                                    Settings
                                </a>
                                <hr class="my-1 border-gray-200 dark:border-gray-700">
                                <a href="/auth/logout" 
                                    class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-sign-out-alt w-5 mr-3 text-red-500"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/auth/login" 
                        class="text-gray-600 dark:text-gray-400 hover:text-primary-500 transition-colors duration-200">Login</a>
                    <a href="/auth/signup" 
                        class="btn-primary text-sm px-4 py-2 hover:shadow-lg transition-all duration-200">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<style>
/* Dropdown animation */
#user-dropdown {
    transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
}

#user-dropdown.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Mobile menu button active state */
#mobile-menu-btn:active {
    transform: scale(0.95);
}
</style>

<script>
// User dropdown functionality
$(document).ready(function() {
    var $userMenuBtn = $('#user-menu-btn');
    var $userDropdown = $('#user-dropdown');
    var $chevron = $userMenuBtn.find('.fa-chevron-down');
    
    function toggleDropdown(show) {
        if (show) {
            $userDropdown.addClass('show');
            $userMenuBtn.attr('aria-expanded', 'true');
            $chevron.css('transform', 'rotate(180deg)');
        } else {
            $userDropdown.removeClass('show');
            $userMenuBtn.attr('aria-expanded', 'false');
            $chevron.css('transform', 'rotate(0deg)');
        }
    }
    
    $userMenuBtn.on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var isOpen = $userDropdown.hasClass('show');
        toggleDropdown(!isOpen);
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$userMenuBtn.is(e.target) && 
            $userMenuBtn.has(e.target).length === 0 && 
            !$userDropdown.is(e.target) && 
            $userDropdown.has(e.target).length === 0) {
            toggleDropdown(false);
        }
    });
    
    // Close on escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $userDropdown.hasClass('show')) {
            toggleDropdown(false);
        }
    });
});
</script>