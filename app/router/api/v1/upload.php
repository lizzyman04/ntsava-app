<?php

use App\Middleware\ApiAuthMiddleware;
use Fluxor\Flow;
use Fluxor\Response;
use Source\Services\StorageService;

Flow::POST()->do(function ($req) {
    // Apply auth middleware
    $auth = new ApiAuthMiddleware();
    $authResult = $auth->handle($req);
    if ($authResult !== null) {
        return $authResult;
    }

    $user = $req->user;
    $apiToken = $req->apiToken;

    // Check permission
    if (!$apiToken->hasPermission('upload')) {
        return Response::error('Token does not have upload permission', 403);
    }

    $file = $_FILES['file'] ?? null;
    $path = $req->input('path', '');

    if (!$file) {
        return Response::error('No file uploaded', 400);
    }

    // Check storage quota
    if (!$user->hasStorageAvailable($file['size'])) {
        return Response::error('Storage quota exceeded', 403, [
            'used' => $user->getStorageUsedBytes(),
            'limit' => $user->getStorageLimitBytes(),
            'used_gb' => round($user->getStorageUsedBytes() / 1073741824, 2),
            'limit_gb' => round($user->getStorageLimitBytes() / 1073741824, 2)
        ]);
    }

    // Upload file
    $storageService = new StorageService();
    $result = $storageService->upload($user, $file, $path);

    if (!$result['success']) {
        return Response::error($result['error'], 500);
    }

    return Response::success([
        'url' => $result['url'],
        'uuid' => $result['uuid'],
        'size' => $result['size'],
        'size_mb' => round($result['size'] / 1048576, 2),
        'mime' => $result['mime'],
        'path' => $result['path']
    ], 'File uploaded successfully');
});