<?php
use Fluxor\View;
use App\Core\Auth;

require_once __DIR__ . '/../components/helpers.php';
?>
<?php View::extend('layouts/auth'); ?>

<?php View::section('title'); ?>
Create Account
<?php View::endSection(); ?>

<?php View::section('subtitle'); ?>
Start your journey with Ntsava
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="grid md:grid-cols-2 gap-8 min-h-[700px]">
    <div class="flex flex-col justify-center overflow-y-auto max-h-[700px]">
        <div class="mb-8">
            <div
                class="w-12 h-12 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center mb-4">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Create your account</h2>
            <p class="text-gray-600 mt-2">Join <?= $stats['total_users'] ?> developers using Ntsava</p>
        </div>

        <form id="signupForm" method="POST" action="/auth/signup">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-primary-500"></i>
                    Full Name
                </label>
                <div class="relative">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="input-field-with-icon" placeholder="Arlindo Abdul" required
                        autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-at mr-2 text-primary-500"></i>
                    Username
                </label>
                <div class="relative">
                    <i class="fas fa-at input-icon"></i>
                    <input type="text" id="username" name="username" class="input-field-with-icon" placeholder="lizzyman04"
                        required>
                </div>
                <div id="usernameError" class="text-xs text-red-500 mt-1 hidden">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    Only letters, numbers, underscore and hyphen
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Only letters, numbers, underscore and hyphen
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-primary-500"></i>
                    Email Address
                </label>
                <div class="relative">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="input-field-with-icon" placeholder="john@example.com"
                        required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-primary-500"></i>
                    Password
                </label>
                <div class="relative">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="input-field-with-icon pr-10"
                        placeholder="••••••••" required minlength="6">
                    <button type="button" id="togglePassword"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <div class="flex gap-1 mb-1">
                        <div class="strength-bar flex-1 bg-gray-200 rounded-full h-1 overflow-hidden">
                            <div id="strengthBar" class="h-full w-0 transition-all duration-300"></div>
                        </div>
                    </div>
                    <p id="strengthText" class="strength-text text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Password must be at least 6 characters
                    </p>
                </div>
            </div>

            <div class="mb-6 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                <div class="flex items-start gap-3">
                    <div class="plan-badge w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-gift text-white text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Free Plan Included</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><i class="fas fa-check-circle text-green-500 mr-2"></i> 1 GB Storage</li>
                            <li><i class="fas fa-check-circle text-green-500 mr-2"></i> 20 GB Monthly Bandwidth</li>
                            <li><i class="fas fa-check-circle text-green-500 mr-2"></i> API Access</li>
                            <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Image Resize & Filters</li>
                        </ul>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-arrow-up mr-1"></i>
                            Upgrade anytime using credits
                        </p>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full">
                <i class="fas fa-user-plus mr-2"></i>
                Create Account
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-600">
                Already have an account?
                <a href="/auth/login" class="text-primary-600 hover:text-primary-700 font-semibold transition-colors">
                    Sign in here
                </a>
            </p>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">
                By creating an account, you agree to our
                <a href="/terms" class="text-primary-500 hover:underline">Terms of Service</a>
                and
                <a href="/privacy" class="text-primary-500 hover:underline">Privacy Policy</a>
            </p>
        </div>
    </div>

    <div class="auth-illustration hidden md:flex flex-col items-center justify-center p-12 text-white">
        <div class="relative z-10 text-center">
            <div class="mb-8">
                <i class="fas fa-rocket text-6xl mb-4 animate-bounce"></i>
                <h3 class="text-2xl font-bold mb-2">Start for Free Today</h3>
                <p class="text-white/90 mb-6">No credit card required. Upgrade as you grow.</p>
            </div>

            <div class="space-y-4 text-left">
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>1GB free storage to start</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>20GB monthly bandwidth</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>Image optimization included</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>API access from day one</span>
                </div>
            </div>

            <div class="mt-8 p-4 bg-white/10 rounded-xl backdrop-blur-sm">
                <p class="text-sm">
                    <i class="fas fa-users mr-2"></i>
                    Join <strong><?= $stats['total_users'] ?></strong> developers already using Ntsava
                </p>
                <p class="text-xs text-white/70 mt-1">
                    <?= $stats['total_files'] ?> files delivered
                </p>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/auth/signup.js') ?>"></script>
<?php View::endSection(); ?>