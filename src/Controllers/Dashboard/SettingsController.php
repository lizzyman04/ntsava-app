<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\User;

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

        // Validate
        if (empty($name) || empty($username) || empty($email)) {
            return Response::error('All fields are required', 400);
        }

        // Check username uniqueness
        $userRepo = ORMHelper::getRepository(User::class);
        $existingUser = $userRepo->findOne(['username' => $username]);
        if ($existingUser && $existingUser->getId() !== $this->user->getId()) {
            return Response::error('Username already taken', 400);
        }

        // Check email uniqueness
        $existingUser = $userRepo->findOne(['email' => $email]);
        if ($existingUser && $existingUser->getId() !== $this->user->getId()) {
            return Response::error('Email already registered', 400);
        }

        // Update user
        $this->user->setName($name)
            ->setUsername($username)
            ->setEmail($email);

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($this->user);
        $entityManager->run();

        // Update session
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
        // Delete all files
        $fileRepo = ORMHelper::getRepository(\Source\Models\File::class);
        $files = $fileRepo->findAll(['userId' => $this->user->getId()]);

        foreach ($files as $file) {
            $absolutePath = base_path('storage/' . $file->getStoragePath());
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }
            $entityManager = ORMHelper::getManager();
            $entityManager->delete($file);
        }

        // Delete tokens
        $tokenRepo = ORMHelper::getRepository(\Source\Models\ApiToken::class);
        $tokens = $tokenRepo->findAll(['userId' => $this->user->getId()]);
        foreach ($tokens as $token) {
            $entityManager = ORMHelper::getManager();
            $entityManager->delete($token);
        }

        // Delete credits
        $creditRepo = ORMHelper::getRepository(\Source\Models\Credit::class);
        $credits = $creditRepo->findOne(['userId' => $this->user->getId()]);
        if ($credits) {
            $entityManager = ORMHelper::getManager();
            $entityManager->delete($credits);
        }

        // Delete roles
        $roleRepo = ORMHelper::getRepository(\Source\Models\UserRole::class);
        $roles = $roleRepo->findAll(['userId' => $this->user->getId()]);
        foreach ($roles as $role) {
            $entityManager = ORMHelper::getManager();
            $entityManager->delete($role);
        }

        // Delete user
        $entityManager = ORMHelper::getManager();
        $entityManager->delete($this->user);
        $entityManager->run();

        // Delete storage directory
        $storagePath = base_path('storage/' . $this->user->getStoragePath());
        if (is_dir($storagePath)) {
            $this->removeDirectory($storagePath);
        }

        // Logout and redirect
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
                unlink($path);
            }
        }

        rmdir($dir);
    }
}