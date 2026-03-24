<?php

namespace App\Middleware;

use App\Core\ORMHelper;
use Source\Models\ApiToken;
use Source\Models\User;
use Fluxor\Response;

class ApiAuthMiddleware
{
    public function handle($request)
    {
        // Get headers
        $userUuid = $request->headers['x-user-uuid'] ?? 
                    $request->headers['X-User-UUID'] ?? 
                    $request->headers['X-User-Id'] ?? null;
        
        $token = $request->headers['x-token'] ?? 
                 $request->headers['X-Token'] ?? 
                 $request->headers['Authorization'] ?? null;

        // Remove Bearer prefix if present
        if ($token && str_starts_with($token, 'Bearer ')) {
            $token = substr($token, 7);
        }

        if (!$userUuid || !$token) {
            return Response::error('Missing authentication headers. Required: X-User-UUID and X-Token', 401);
        }

        // Hash token for comparison
        $tokenHash = hash('sha256', $token);

        // Find token
        $tokenRepo = ORMHelper::getRepository(ApiToken::class);
        $apiToken = $tokenRepo->findOne(['tokenHash' => $tokenHash]);

        if (!$apiToken) {
            return Response::error('Invalid token', 401);
        }

        // Check expiration
        if ($apiToken->isExpired()) {
            return Response::error('Token expired', 401);
        }

        // Get user
        $userRepo = ORMHelper::getRepository(User::class);
        $user = $userRepo->findByPK($apiToken->getUserId());

        if (!$user) {
            return Response::error('User not found', 401);
        }

        if (!$user->isActive()) {
            return Response::error('User account is suspended', 403);
        }

        // Update token last used
        $apiToken->touch();
        $entityManager = ORMHelper::getManager();
        $entityManager->persist($apiToken);
        $entityManager->run();

        // Attach user and token to request
        $request->user = $user;
        $request->apiToken = $apiToken;

        return null; // Continue
    }
}