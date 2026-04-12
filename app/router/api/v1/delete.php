<?php

use Fluxor\Flow;
use Fluxor\Response;
use Source\Services\StorageService;
use App\Middleware\ApiAuthMiddleware;

Flow::DELETE()->do(function ($req) {
    $auth = new ApiAuthMiddleware();
    $authResult = $auth->handle($req);
    if ($authResult !== null) {
        return $authResult;
    }

    $user = $req->getAttribute('user');
    $apiToken = $req->getAttribute('apiToken');

    if (!$apiToken->hasPermission('delete')) {
        return Response::error('Token does not have delete permission', 403);
    }

    $path = $req->input('path');
    $uuid = $req->input('uuid');

    if (!$path && !$uuid) {
        return Response::error('Either path or uuid parameter is required', 400);
    }

    $identifier = $path ?: $uuid;

    $storageService = new StorageService();
    $result = $storageService->delete($user, $identifier);

    if (!$result['success']) {
        return Response::error($result['error'], 404);
    }

    return Response::success(null, 'File deleted successfully');
});