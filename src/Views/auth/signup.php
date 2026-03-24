<?php use Fluxor\View; ?>
<?php View::extend('layouts/auth'); ?>

<?php View::section('title'); ?>
Create Account
<?php View::endSection(); ?>

<?php View::section('subtitle'); ?>
Start using our CDN service
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .plan-info {
        background-color: #f9fafb;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<form id="signupForm" method="POST" action="/auth/signup">
    <?= csrf_field() ?>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
        <input type="text" name="name" class="input-field" required autofocus>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
        <input type="text" name="username" class="input-field" required>
        <p class="text-xs text-gray-500 mt-1">Only letters, numbers, underscore and hyphen</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
        <input type="email" name="email" class="input-field" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
        <input type="password" name="password" class="input-field" required minlength="6">
        <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
    </div>

    <div class="plan-info">
        <p class="text-sm font-semibold text-gray-700 mb-2">Free Plan Included:</p>
        <ul class="text-sm text-gray-600 space-y-1">
            <li><i class="fas fa-check text-green-500 mr-2"></i> 1 GB Storage</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i> 20 GB Monthly Bandwidth</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i> API Access</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i> Image Resize & Filters</li>
        </ul>
        <p class="text-xs text-gray-500 mt-2">You can upgrade anytime using credits</p>
    </div>

    <button type="submit" class="btn-auth">
        <i class="fas fa-user-plus mr-2"></i> Create Account
    </button>
</form>

<div class="mt-6 text-center">
    <p class="text-gray-600">
        Already have an account?
        <a href="/auth/login" class="text-purple-600 hover:text-purple-800 font-semibold">
            Sign in
        </a>
    </p>
</div>

<script src="<?= asset('assets/js/auth/signup.js') ?>"></script>
<?php View::endSection(); ?>