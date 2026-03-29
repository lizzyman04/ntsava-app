<?php
use App\Core\Auth;
use Fluxor\View;
use App\Core\ORMHelper;
use Source\Models\UserRole;

require_once __DIR__ . '/../components/helpers.php';

$userData = Auth::user();
$isAdmin = false;

if ($userData) {
    $roleRepo = ORMHelper::getRepository(UserRole::class);
    $adminRole = $roleRepo->findOne(['userId' => $userData['id'], 'role' => 'admin']);
    $isAdmin = !is_null($adminRole);
}

$userObj = isset($user) ? $user : null;
$totalFiles = 0;
$credits = 0;

if ($userObj) {
    $allFiles = ORMHelper::findAll(\Source\Models\File::class);
    $totalFiles = 0;
    foreach ($allFiles as $f) {
        if ($f->getUserId() === $userObj->getId() && !$f->isDeleted()) {
            $totalFiles++;
        }
    }
    
    $allCredits = ORMHelper::findAll(\Source\Models\Credit::class);
    foreach ($allCredits as $c) {
        if ($c->getUserId() === $userObj->getId()) {
            $credits = $c->getAmount();
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= View::yield('title', 'Dashboard') ?> - Ntsava</title>

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

    <meta name="csrf-token" content="<?= Auth::csrfToken() ?>">
</head>

<body class="bg-gray-50">
    <?php component('sidebar', [
        'user' => $userData,
        'total_files' => $totalFiles,
        'credits' => $credits,
        'active_menu' => View::yield('active_menu'),
        'is_admin' => $isAdmin
    ]); ?>
    <?php component('mobile-sidebar', ['user' => $userData]); ?>

    <div class="lg:ml-72 min-h-screen">
        <?php component('header', ['user' => $userData]); ?>

        <main class="p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <?= View::yield('content') ?>
            </div>
        </main>
    </div>

    <div id="toast" style="display: none;" class="toast"></div>

    <?php
    $activeMenu = View::yield('active_menu');
    if ($activeMenu === 'upload'):
        ?>
        <script src="<?= asset('js/dashboard/upload.js') ?>"></script>
    <?php elseif ($activeMenu === 'files'): ?>
        <script src="<?= asset('js/dashboard/files.js') ?>"></script>
    <?php elseif ($activeMenu === 'tokens'): ?>
        <script src="<?= asset('js/dashboard/tokens.js') ?>"></script>
    <?php elseif ($activeMenu === 'credits'): ?>
        <script src="<?= asset('js/dashboard/credits.js') ?>"></script>
    <?php elseif ($activeMenu === 'dashboard'): ?>
        <script src="<?= asset('js/dashboard/index.js') ?>"></script>
    <?php endif; ?>

    <?= View::yield('scripts') ?>
</body>

</html>