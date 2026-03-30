<?php

namespace Source\Controllers\Dashboard;

use Fluxor\Response;
use App\Core\ORMHelper;
use Source\Models\ApiToken;

class TokensController extends DashboardController
{
    public function index()
    {
        $allTokens = ORMHelper::findAll(ApiToken::class);
        $tokens = [];

        foreach ($allTokens as $token) {
            if ($token->getUserId() === $this->user->getId()) {
                $tokens[] = $token;
            }
        }

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
        try {
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
            $permissionsJson = json_encode($permissions);

            $db = ORMHelper::getDatabaseManager();
            $db->database('default')->insert('api_tokens')->values([
                'user_id' => $this->user->getId(),
                'token_hash' => $tokenHash,
                'name' => $name,
                'permissions' => $permissionsJson,
                'created_at' => date('Y-m-d H:i:s')
            ])->run();

            return Response::success([
                'token' => $token,
                'name' => $name,
                'permissions' => $permissions
            ], 'Token created successfully');
            
        } catch (\Exception $e) {
            return Response::error('Server error: ' . $e->getMessage(), 500);
        }
    }

    public function revoke($id)
    {
        try {
            $allTokens = ORMHelper::findAll(ApiToken::class);
            $token = null;

            foreach ($allTokens as $t) {
                if ($t->getId() === (int) $id && $t->getUserId() === $this->user->getId()) {
                    $token = $t;
                    break;
                }
            }

            if (!$token) {
                return Response::error('Token not found', 404);
            }

            $entityManager = ORMHelper::getManager();
            $entityManager->delete($token);
            $entityManager->run();

            return Response::success(null, 'Token revoked successfully');
            
        } catch (\Exception $e) {
            return Response::error('Server error: ' . $e->getMessage(), 500);
        }
    }
}