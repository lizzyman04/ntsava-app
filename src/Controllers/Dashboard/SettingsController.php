<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\User;
use Source\Models\File;
use Source\Models\ApiToken;
use Source\Models\Credit;
use Source\Models\UserRole;

class SettingsController extends DashboardController
{
    public function index()
    {
        return Response::view('dashboard/settings', [
            'title' => 'Settings',
            'page_title' => 'Account Settings',
            'active_menu' => 'settings',
            'user' => $this->user,
            'userData' => $this->userData
        ]);
    }

    public function updateProfile()
    {
        $name = $this->request->input('name');
        $username = $this->request->input('username');
        $email = $this->request->input('email');

        if (empty($name) || empty($username) || empty($email)) {
            return Response::error('All fields are required', 400);
        }

        $allUsers = ORMHelper::findAll(User::class);
        foreach ($allUsers as $user) {
            if ($user->getUsername() === $username && $user->getId() !== $this->user->getId()) {
                return Response::error('Username already taken', 400);
            }
            if ($user->getEmail() === $email && $user->getId() !== $this->user->getId()) {
                return Response::error('Email already registered', 400);
            }
        }

        $this->user->setName($name)
            ->setUsername($username)
            ->setEmail($email);

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($this->user);
        $entityManager->run();

        $_SESSION['auth_credentials']['name'] = $name;
        $_SESSION['auth_credentials']['username'] = $username;
        $_SESSION['auth_credentials']['email'] = $email;

        return Response::success(null, 'Profile updated successfully');
    }

    public function updatePassword()
    {
        $currentPassword = $this->request->input('current_password');
        $newPassword = $this->request->input('new_password');

        if (!password_verify($currentPassword, $this->user->getPasswordHash())) {
            return Response::error('Current password is incorrect', 400);
        }

        if (strlen($newPassword) < 6) {
            return Response::error('New password must be at least 6 characters', 400);
        }

        $this->user->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($this->user);
        $entityManager->run();

        return Response::success(null, 'Password updated successfully');
    }

    public function deleteAccount()
    {
        $entityManager = ORMHelper::getManager();

        $allFiles = ORMHelper::findAll(File::class);
        foreach ($allFiles as $file) {
            if ($file->getUserId() === $this->user->getId()) {
                $storagePath = $file->getStoragePath();
                if ($storagePath) {
                    $absolutePath = base_path('storage/' . $storagePath);
                    if (file_exists($absolutePath) && is_file($absolutePath)) {
                        unlink($absolutePath);
                    }
                }
                $entityManager->delete($file);
            }
        }

        $allTokens = ORMHelper::findAll(ApiToken::class);
        foreach ($allTokens as $token) {
            if ($token->getUserId() === $this->user->getId()) {
                $entityManager->delete($token);
            }
        }

        $allCredits = ORMHelper::findAll(Credit::class);
        foreach ($allCredits as $credit) {
            if ($credit->getUserId() === $this->user->getId()) {
                $entityManager->delete($credit);
            }
        }

        $allRoles = ORMHelper::findAll(UserRole::class);
        foreach ($allRoles as $role) {
            if ($role->getUserId() === $this->user->getId()) {
                $entityManager->delete($role);
            }
        }

        $entityManager->delete($this->user);
        $entityManager->run();

        $storagePath = base_path('storage/' . $this->user->getStoragePath());
        if (is_dir($storagePath)) {
            $this->removeDirectory($storagePath);
        }

        \App\Core\Auth::logout();

        return Response::success([
            'redirect' => '/'
        ], 'Account deleted successfully');
    }

    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }

        rmdir($dir);
    }
}