<?php

use Fluxor\Flow;
use Fluxor\Response;
use Source\Models\User;
use App\Core\ORMHelper;
use App\Middleware\ApiAuthMiddleware;
use Source\Services\ResizeService;

Flow::GET()->do(function($req) {
    // Apply auth middleware (optional for resize - can be public)
    $auth = new ApiAuthMiddleware();
    $authResult = $auth->handle($req);
    
    // If not authenticated, we need user to provide user info
    if ($authResult !== null) {
        // Try to get user from query
        $username = $req->input('user');
        $path = $req->input('path');
        
        if (!$username || !$path) {
            return Response::error('Authentication required or provide user and path', 401);
        }
        
        $userRepo = ORMHelper::getRepository(User::class);
        $user = $userRepo->findOne(['username' => $username]);
        
        if (!$user || !$user->isActive()) {
            return Response::error('User not found', 404);
        }
    } else {
        $user = $req->user;
        $path = $req->input('path');
    }
    
    // Get path
    $path = $path ?? $req->input('path');
    
    if (!$path) {
        return Response::error('Path parameter is required', 400);
    }
    
    // Get resize parameters
    $params = [
        'w' => $req->input('w'),
        'h' => $req->input('h'),
        'format' => $req->input('format'),
        'filter' => $req->input('filter'),
        'blur' => $req->input('blur'),
        'brightness' => $req->input('brightness'),
        'contrast' => $req->input('contrast'),
        'sharpen' => $req->input('sharpen')
    ];
    
    // Remove null values
    $params = array_filter($params);
    
    // Process resize
    $resizeService = new ResizeService();
    $result = $resizeService->process($user, $path, $params);
    
    if (!$result['success']) {
        return Response::error($result['error'], 400);
    }
    
    // Redirect to cache URL or serve directly
    return Response::redirect($result['url']);
});