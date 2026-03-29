<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Controller;
use App\Core\Auth;
use App\Core\ORMHelper;
use Source\Models\User;

abstract class DashboardController extends Controller
{
    protected $user;
    protected $userData;

    public function __construct()
    {
        $this->userData = Auth::requireAuth();

        $allUsers = ORMHelper::findAll(User::class);
        $this->user = null;
        foreach ($allUsers as $user) {
            if ($user->getId() === $this->userData['id']) {
                $this->user = $user;
                break;
            }
        }

        if (!$this->user) {
            Auth::logout();
            header('Location: /auth/login');
            exit;
        }
    }

    protected function getUser()
    {
        return $this->user;
    }

    protected function getUserData()
    {
        return $this->userData;
    }
}