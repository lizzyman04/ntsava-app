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
        $userRepo = ORMHelper::getRepository(User::class);
        $this->user = $userRepo->findOne(['id' => $this->userData['id']]);

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