<?php

namespace Source\Services;

use App\Core\ORMHelper;
use Source\Models\ApiToken;

class TokenService
{
    public function createToken(int $userId, string $name, array $permissions = ['upload', 'delete', 'read'], ?int $expiresInDays = null): array
    {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        
        $apiToken = new ApiToken($userId, $name, $tokenHash);
        $apiToken->setPermissions($permissions);
        
        if ($expiresInDays) {
            $expiresAt = new \DateTime();
            $expiresAt->modify("+{$expiresInDays} days");
            $apiToken->setExpiresAt($expiresAt);
        }
        
        $entityManager = ORMHelper::getManager();
        $entityManager->persist($apiToken);
        $entityManager->run();
        
        return [
            'id' => $apiToken->getId(),
            'token' => $token,
            'name' => $name,
            'permissions' => $permissions,
            'expires_at' => $apiToken->getExpiresAt()?->format('Y-m-d H:i:s')
        ];
    }
    
    public function listTokens(int $userId): array
    {
        $tokenRepo = ORMHelper::getRepository(ApiToken::class);
        $allTokens = $tokenRepo->findAll();
        $tokens = [];
        
        foreach ($allTokens as $token) {
            if ($token->getUserId() === $userId) {
                $tokens[] = [
                    'id' => $token->getId(),
                    'name' => $token->getName(),
                    'permissions' => $token->getPermissions(),
                    'last_used_at' => $token->getLastUsedAt()?->format('Y-m-d H:i:s'),
                    'expires_at' => $token->getExpiresAt()?->format('Y-m-d H:i:s'),
                    'created_at' => $token->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }
        }
        
        return $tokens;
    }
    
    public function revokeToken(int $userId, int $tokenId): bool
    {
        $tokenRepo = ORMHelper::getRepository(ApiToken::class);
        $allTokens = $tokenRepo->findAll();
        $token = null;
        
        foreach ($allTokens as $t) {
            if ($t->getId() === $tokenId && $t->getUserId() === $userId) {
                $token = $t;
                break;
            }
        }
        
        if (!$token) {
            return false;
        }
        
        $entityManager = ORMHelper::getManager();
        $entityManager->delete($token);
        $entityManager->run();
        
        return true;
    }
}