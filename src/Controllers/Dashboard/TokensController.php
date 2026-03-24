<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\ApiToken;

class TokensController extends DashboardController
{
    public function index()
    {
        $tokenRepo = ORMHelper::getRepository(ApiToken::class);
        $tokens = $tokenRepo->findAll(['userId' => $this->user->getId()]);

        return Response::view('dashboard/tokens', [
            'title' => 'API Tokens',
            'page_title' => 'API Tokens',
            'active_menu' => 'tokens',
            'user' => $this->user,
            'tokens' => $tokens
        ]);
    }

    public function create()
    {
        $name = $this->request->input('name');
        $permissions = $this->request->input('permissions');

        if (empty($permissions)) {
            $permissions = ['upload', 'delete', 'read'];
        } elseif (is_string($permissions)) {
            $permissions = json_decode($permissions, true);
            if (!is_array($permissions)) {
                $permissions = explode(',', $permissions);
            }
        }

        if (empty($name)) {
            return Response::error('Token name is required', 400);
        }

        $validPermissions = ['upload', 'delete', 'read'];
        foreach ($permissions as $perm) {
            if (!in_array($perm, $validPermissions)) {
                return Response::error('Invalid permission: ' . $perm, 400);
            }
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $apiToken = new ApiToken($this->user->getId(), $name, $tokenHash);
        $apiToken->setPermissions($permissions);

        $entityManager = ORMHelper::getManager();
        $entityManager->persist($apiToken);
        $entityManager->run();

        return Response::success([
            'token' => $token,
            'name' => $name,
            'permissions' => $permissions
        ], 'Token created successfully');
    }

    public function revoke($id)
    {
        $tokenRepo = ORMHelper::getRepository(ApiToken::class);
        $token = $tokenRepo->findOne(['id' => (int) $id, 'userId' => $this->user->getId()]);

        if (!$token) {
            return Response::error('Token not found', 404);
        }

        $entityManager = ORMHelper::getManager();
        $entityManager->delete($token);
        $entityManager->run();

        return Response::success(null, 'Token revoked successfully');
    }
}