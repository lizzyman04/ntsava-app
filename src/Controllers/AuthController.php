<?php

namespace Source\Controllers;

use App\Core\ORMHelper;
use Source\Models\Plan;
use App\Core\Auth;
use Fluxor\Controller;
use Fluxor\Response;
use Source\Models\User;
use Source\Models\UserRole;
use Source\Models\Credit;
use Source\Models\File;

class AuthController extends Controller
{
    public function showLogin()
    {
        $stats = $this->getPublicStats();

        return Response::view('auth/login', [
            'title' => 'Login',
            'subtitle' => 'Access your Ntsava account',
            'stats' => $stats
        ]);
    }

    public function showSignup()
    {
        $stats = $this->getPublicStats();

        return Response::view('auth/signup', [
            'title' => 'Create Account',
            'subtitle' => 'Start using Ntsava services',
            'plans' => $this->getPlans(),
            'stats' => $stats
        ]);
    }

    public function login()
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $remember = $this->request->input('remember') === 'on';

        $user = ORMHelper::findOneBy(User::class, 'email', $email);

        if (!$user || !password_verify($password, $user->getPasswordHash())) {
            return Response::error('Invalid email or password', 401);
        }

        if (!$user->isActive()) {
            return Response::error('Your account is suspended. Please contact support.', 403);
        }

        $user->setLastLoginAt(new \DateTime());
        $entityManager = ORMHelper::getManager();
        $entityManager->persist($user);
        $entityManager->run();

        Auth::login([
            'id' => $user->getId(),
            'uuid' => $user->getUuid(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'name' => $user->getName()
        ], $remember);

        return Response::success([
            'redirect' => '/dashboard'
        ], 'Logged in successfully');
    }

    public function signup()
    {
        $name = $this->request->input('name');
        $username = $this->request->input('username');
        $email = $this->request->input('email');
        $password = $this->request->input('password');

        $errors = [];

        if (empty($name))
            $errors['name'] = 'Name is required';
        if (empty($username))
            $errors['username'] = 'Username is required';
        if (empty($email))
            $errors['email'] = 'Email is required';
        if (strlen($password) < 6)
            $errors['password'] = 'Password must be at least 6 characters';

        if (!empty($username) && !preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, underscore and hyphen';
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        $existingUser = ORMHelper::findOneBy(User::class, 'username', $username);
        if ($existingUser) {
            $errors['username'] = 'Username already taken';
        }

        $existingUser = ORMHelper::findOneBy(User::class, 'email', $email);
        if ($existingUser) {
            $errors['email'] = 'Email already registered';
        }

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $plan = ORMHelper::findOneBy(Plan::class, 'slug', 'free');

        if (!$plan) {
            return Response::error('Free plan not found. Please contact administrator.', 500);
        }

        $user = new User();
        $user->setName($name)
            ->setUsername($username)
            ->setEmail($email)
            ->setPasswordHash(password_hash($password, PASSWORD_DEFAULT))
            ->setPlanId($plan->getId())
            ->setStorageLimitBytes($plan->getStorageLimitBytes())
            ->setBandwidthLimitBytes($plan->getBandwidthLimitBytes());

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($user);
        $entityManager->run();

        $userRole = new UserRole($user->getId(), 'user');
        $entityManager->persist($userRole);

        $credits = new Credit($user->getId());
        $entityManager->persist($credits);
        $entityManager->run();

        $storagePath = base_path('storage/' . $user->getStoragePath());
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        return Response::success([
            'redirect' => '/auth/login'
        ], 'Account created successfully! Please login.');
    }

    public function logout()
    {
        Auth::logout();
        return Response::redirect('/');
    }

    private function getPlans(): array
    {
        $allPlans = ORMHelper::findAll(Plan::class);
        $activePlans = [];

        foreach ($allPlans as $plan) {
            if ($plan->isActive()) {
                $activePlans[] = $plan;
            }
        }

        usort($activePlans, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        return $activePlans;
    }

    private function getPublicStats(): array
    {
        $totalUsers = count(ORMHelper::findAll(User::class));
        $allFiles = ORMHelper::findAll(File::class);

        $totalFiles = 0;
        foreach ($allFiles as $file) {
            if (!$file->isDeleted()) {
                $totalFiles++;
            }
        }

        return [
            'total_users' => $this->formatNumber($totalUsers),
            'total_files' => $this->formatNumber($totalFiles),
            'app_name' => 'Ntsava',
            'tagline' => 'Everything you need, in one basket'
        ];
    }

    private function formatNumber($number): string
    {
        if ($number < 100) {
            return '~ 100';
        }

        if ($number < 1000) {
            $rounded = ceil($number / 100) * 100;
            return $rounded . '+';
        }

        if ($number < 5000) {
            $rounded = ceil($number / 1000) * 1000;
            return $rounded . '+';
        }

        $rounded = ceil($number / 1000);
        return $rounded . 'k+';
    }
}