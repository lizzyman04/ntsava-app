<?php 
use Fluxor\View;
use App\Core\Auth;

require_once __DIR__ . '/../components/helpers.php';
?>
<?php View::extend('layouts/auth'); ?>

<?php View::section('title'); ?>
Login
<?php View::endSection(); ?>

<?php View::section('subtitle'); ?>
Welcome back! Access your Ntsava account
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<div class="grid md:grid-cols-2 gap-8 min-h-[600px]">
    <div class="flex flex-col justify-center">
        <div class="mb-8">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center mb-4">
                <i class="fas fa-cloud-upload-alt text-white text-xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Sign in to your account</h2>
            <p class="text-gray-600 mt-2">Enter your credentials to access your dashboard</p>
        </div>

        <form id="loginForm" method="POST" action="/auth/login">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= View::e($redirect ?? '/dashboard') ?>">

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-primary-500"></i>
                    Email Address
                </label>
                <div class="relative">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" 
                           name="email" 
                           class="input-field-with-icon"
                           placeholder="you@example.com"
                           required 
                           autofocus>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-primary-500"></i>
                    Password
                </label>
                <div class="relative">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           class="input-field-with-icon"
                           placeholder="••••••••"
                           required>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="remember-checkbox">
                    <span class="text-sm text-gray-600">Remember me</span>
                </label>
                <a href="/forgot-password" class="text-sm text-primary-600 hover:text-primary-700 transition-colors">
                    Forgot password?
                </a>
            </div>

            <button type="submit" class="btn-primary w-full">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-gray-600">
                Don't have an account?
                <a href="/auth/signup" class="text-primary-600 hover:text-primary-700 font-semibold transition-colors">
                    Create one now
                </a>
            </p>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">
                By signing in, you agree to our 
                <a href="/terms" class="text-primary-500 hover:underline">Terms of Service</a> 
                and 
                <a href="/privacy" class="text-primary-500 hover:underline">Privacy Policy</a>
            </p>
        </div>
    </div>

    <div class="auth-illustration hidden md:flex flex-col items-center justify-center p-12 text-white">
        <div class="relative z-10 text-center">
            <div class="mb-8">
                <i class="fas fa-cloud-upload-alt text-6xl mb-4 animate-pulse"></i>
                <h3 class="text-2xl font-bold mb-2">Welcome to <?= htmlspecialchars($stats['app_name']) ?></h3>
                <p class="text-white/90 mb-6"><?= htmlspecialchars($stats['tagline']) ?></p>
            </div>
            
            <div class="space-y-4 text-left">
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-users text-xl"></i>
                    <span><?= $stats['total_users'] ?> active users</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-file-alt text-xl"></i>
                    <span><?= $stats['total_files'] ?> files delivered</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-bolt text-xl"></i>
                    <span>Lightning fast delivery</span>
                </div>
                <div class="feature-item flex items-center gap-3">
                    <i class="fas fa-shield-alt text-xl"></i>
                    <span>Enterprise-grade security</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/auth/login.js') ?>"></script>
<?php View::endSection(); ?>