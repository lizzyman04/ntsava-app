<?php
/**
 * Mobile Sidebar Component
 * 
 * @var array|null $user Authenticated user data
 */
?>
<aside
    class="sidebar fixed left-0 top-0 h-full w-72 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 z-50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center">
                    <i class="fas fa-cloud-upload-alt text-white"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-800">Ntsava</h2>
            </div>
            <button id="close-sidebar" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="space-y-2">
            <?php if (\App\Core\Auth::check()): ?>
                <a href="/dashboard"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-tachometer-alt w-5 text-gray-400"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/dashboard/files"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-folder-open w-5 text-gray-400"></i>
                    <span>My Files</span>
                </a>
                <a href="/dashboard/upload"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-cloud-upload-alt w-5 text-gray-400"></i>
                    <span>Upload</span>
                </a>
                <a href="/dashboard/tokens"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-key w-5 text-gray-400"></i>
                    <span>API Tokens</span>
                </a>
                <a href="/dashboard/credits"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-coins w-5 text-gray-400"></i>
                    <span>Credits</span>
                </a>
                <hr class="my-4 border-gray-200">
                <a href="/about"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-info-circle w-5 text-gray-400"></i>
                    <span>About</span>
                </a>
                <a href="/contact"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    <span>Contact</span>
                </a>
                <hr class="my-4 border-gray-200">
                <a href="/auth/logout"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 transition-all duration-200">
                    <i class="fas fa-sign-out-alt w-5 text-red-500"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <a href="/"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-home w-5 text-gray-400"></i>
                    <span>Home</span>
                </a>
                <a href="/about"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-info-circle w-5 text-gray-400"></i>
                    <span>About</span>
                </a>
                <a href="/docs"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-book w-5 text-gray-400"></i>
                    <span>API Documentation</span>
                </a>
                <a href="/terms"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-file-contract w-5 text-gray-400"></i>
                    <span>Terms of Service</span>
                </a>
                <a href="/privacy"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-shield-alt w-5 text-gray-400"></i>
                    <span>Privacy Policy</span>
                </a>
                <a href="/contact"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-envelope w-5 text-gray-400"></i>
                    <span>Contact</span>
                </a>
                <hr class="my-4 border-gray-200">
                <a href="/auth/login"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary-600 hover:bg-primary-50 transition-all duration-200">
                    <i class="fas fa-sign-in-alt w-5 text-primary-500"></i>
                    <span>Login</span>
                </a>
                <a href="/auth/signup"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary-600 hover:bg-primary-50 transition-all duration-200">
                    <i class="fas fa-user-plus w-5 text-primary-500"></i>
                    <span>Sign Up</span>
                </a>
            <?php endif; ?>
        </nav>
    </div>
</aside>