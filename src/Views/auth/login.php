<?php use App\Core\Auth;
      use Fluxor\View; ?>
<?php View::extend('layouts/auth'); ?>

<?php View::section('title'); ?>
Login
<?php View::endSection(); ?>

<?php View::section('subtitle'); ?>
Access your account
<?php View::endSection(); ?>

<?php View::section('styles'); ?>
<style>
    .remember {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .remember input {
        width: auto;
    }
</style>
<?php View::endSection(); ?>

<?php View::section('content'); ?>
<form id="loginForm" method="POST" action="/auth/login">
    <?= View::csrfField() ?>
        <input type="hidden" name="redirect" value="<?= View::e($redirect ?? '/') ?>">

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
        <input type="email" name="email" class="input-field" required autofocus>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
        <input type="password" name="password" class="input-field" required>
    </div>

    <div class="remember">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember" class="text-sm text-gray-600">Remember me</label>
    </div>

    <button type="submit" class="btn-auth">
        <i class="fas fa-sign-in-alt mr-2"></i> Sign In
    </button>
</form>

<div class="mt-6 text-center">
    <p class="text-gray-600">
        Don't have an account?
        <a href="/auth/signup" class="text-purple-600 hover:text-purple-800 font-semibold">
            Sign up
        </a>
    </p>
</div>

<script src="<?= asset('js/auth/login.js') ?>"></script>
<?php View::endSection(); ?>