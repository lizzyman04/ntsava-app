<?php 
use Fluxor\View;
require_once __DIR__ . '/../components/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= View::yield('title', 'Ntsava') ?> - Ntsava</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <?= View::yield('styles') ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Main JS -->
    <script src="<?= asset('js/app.js') ?>"></script>

    <meta name="csrf-token" content="<?= \App\Core\Auth::csrfToken() ?>">
</head>

<body class="bg-gray-50">
    <?php component('header', ['user' => \App\Core\Auth::user()]); ?>
    <?php component('mobile-sidebar', ['user' => \App\Core\Auth::user()]); ?>

    <main class="container mx-auto px-4 py-8 min-h-screen">
        <?= View::yield('content') ?>
    </main>

    <?php component('footer'); ?>
    
    <div id="toast" style="display: none;" class="toast"></div>

    <?= View::yield('scripts') ?>
</body>

</html>