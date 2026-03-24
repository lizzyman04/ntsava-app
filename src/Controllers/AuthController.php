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

class AuthController extends Controller
{
    public function showLogin()
    {
        return Response::view('auth/login', [
            'title' => 'Login',
            'subtitle' => 'Access your account'
        ]);
    }

    public function showSignup()
    {
        return Response::view('auth/signup', [
            'title' => 'Create Account',
            'subtitle' => 'Start using our CDN service',
            'plans' => $this->getPlans()
        ]);
    }

    public function login()
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $remember = $this->request->input('remember') === 'on';

        $userRepo = ORMHelper::getRepository(User::class);
        $user = $userRepo->findOne(['email' => $email]);

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

        // Validation
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }
        if (empty($username)) {
            $errors['username'] = 'Username is required';
        }
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        }
        if (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        // Validate username format (only letters, numbers, underscore, hyphen)
        if (!empty($username) && !preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, underscore and hyphen';
        }

        // Validate email format
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        // Check if username exists
        $userRepo = ORMHelper::getRepository(User::class);
        if ($userRepo->findOne(['username' => $username])) {
            $errors['username'] = 'Username already taken';
        }

        // Check if email exists
        if ($userRepo->findOne(['email' => $email])) {
            $errors['email'] = 'Email already registered';
        }

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        // Get Free plan (slug = 'free')
        $planRepo = ORMHelper::getRepository(Plan::class);
        $plan = $planRepo->findOne(['slug' => 'free']);

        if (!$plan) {
            return Response::error('Free plan not found. Please contact administrator.', 500);
        }

        // Create user with Free plan
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

        // Add default role
        $userRole = new UserRole($user->getId(), 'user');
        $entityManager->persist($userRole);

        // Create credits record (0 credits initially)
        $credits = new Credit($user->getId());
        $entityManager->persist($credits);
        $entityManager->run();

        // Create storage directory
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
        $planRepo = ORMHelper::getRepository(Plan::class);
        $plans = $planRepo->findAll(['isActive' => true]);

        usort($plans, function ($a, $b) {
            return $a->getSortOrder() <=> $b->getSortOrder();
        });

        return $plans;
    }
}