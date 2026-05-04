<?php

use App\Middleware\ApiAuthMiddleware;
use Fluxor\Flow;
use Fluxor\Response;
use Source\Services\StorageService;

Flow::cors([
    'allowed_origins' => ['*'],
    'allowed_methods' => ['POST', 'OPTIONS'],
    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'X-Token',
        'X-User-Uuid'
    ],
    'max_age' => 86400
]);

Flow::OPTIONS()->do(function($req) {
    return Response::text('', 204);
});

Flow::POST()->do(function ($req) {
    $auth = new ApiAuthMiddleware();
    $authResult = $auth->handle($req);
    if ($authResult !== null) {
        return $authResult;
    }

    $user = $req->getAttribute('user');
    $apiToken = $req->getAttribute('apiToken');

    if (!$apiToken->hasPermission('upload')) {
        return Response::error('Token does not have upload permission', 403);
    }

    $file = $_FILES['file'] ?? null;
    $path = $req->input('path', '');

    if (!$file) {
        return Response::error('No file uploaded', 400);
    }

    if (!$user->hasStorageAvailable($file['size'])) {
        return Response::error('Storage quota exceeded', 403, [
            'used' => $user->getStorageUsedBytes(),
            'limit' => $user->getStorageLimitBytes(),
            'used_gb' => round($user->getStorageUsedBytes() / 1073741824, 2),
            'limit_gb' => round($user->getStorageLimitBytes() / 1073741824, 2)
        ]);
    }

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