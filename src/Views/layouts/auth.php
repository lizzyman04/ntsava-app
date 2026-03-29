<?php
use App\Core\Auth;
use Fluxor\View;
require_once __DIR__ . '/../components/helpers.php';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::yield('title', 'Authentication') ?> - Ntsava App</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">

    <?= View::yield('styles') ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Main JS -->
    <script src="<?= asset('js/app.js') ?>"></script>

    <meta name="csrf-token" content="<?= Auth::csrfToken() ?>">
</head>

<body>
    <div class="auth-container flex items-center justify-center px-4 py-8">
        <div class="auth-card">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800"><?= View::yield('title', 'Ntsava App') ?></h1>
                <p class="text-gray-600 mt-2"><?= View::yield('subtitle', '') ?></p>
            </div>

            <?= View::yield('content') ?>
        </div>
    </div>

    <?= View::yield('scripts') ?>
</body>

</html>