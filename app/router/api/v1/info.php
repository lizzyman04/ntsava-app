<?php

use Fluxor\Flow;
use Fluxor\Response;
use Source\Services\StorageService;
use App\Middleware\ApiAuthMiddleware;

Flow::GET()->do(function($req) {
    // Apply auth middleware
    $auth = new ApiAuthMiddleware();
    $authResult = $auth->handle($req);
    if ($authResult !== null) {
        return $authResult;
    }
    
    $user = $req->user;
    $apiToken = $req->apiToken;
    
    // Check permission
    if (!$apiToken->hasPermission('read')) {
        return Response::error('Token does not have read permission', 403);
    }
    
    $path = $req->input('path');
    $uuid = $req->input('uuid');
    
    if (!$path && !$uuid) {
        return Response::error('Either path or uuid parameter is required', 400);
    }
    
    $identifier = $path ?: $uuid;
    
    $storageService = new StorageService();
    $result = $storageService->getInfo($user, $identifier);
    
    if (!$result['success']) {
        return Response::error($result['error'], 404);
    }
    
    return Response::success($result['data'], 'File info retrieved');
});