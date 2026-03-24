<?php
use App\Core\Auth;
use Fluxor\View;
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::yield('title', 'Authentication') ?> - CDN App</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        .auth-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .auth-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 28rem;
            width: 100%;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-field {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            outline: none;
        }

        .input-field:focus {
            ring: 2px solid #8b5cf6;
            border-color: transparent;
        }

        .btn-auth {
            width: 100%;
            background: linear-gradient(to right, #7c3aed, #3b82f6);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-auth:hover {
            background: linear-gradient(to right, #6d28d9, #2563eb);
        }
    </style>

    <?= View::yield('styles') ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <meta name="csrf-token" content="<?= Auth::csrfToken() ?>">
</head>

<body>
    <div class="auth-container flex items-center justify-center px-4">
        <div class="auth-card">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800"><?= View::yield('title', 'CDN App') ?></h1>
                <p class="text-gray-600 mt-2"><?= View::yield('subtitle', '') ?></p>
            </div>

            <?= View::yield('content') ?>
        </div>
    </div>

    <?= View::yield('scripts') ?>
</body>

</html>