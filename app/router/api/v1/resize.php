<?php

use Fluxor\Flow;
use Fluxor\Response;
use Source\Models\User;
use App\Core\ORMHelper;
use Source\Services\ResizeService;

Flow::GET()->do(function ($req) {
    $photo = $req->input('photo');

    if (!$photo) {
        return Response::error('Photo parameter is required. Format: u/username/path/to/file', 400);
    }

    if (!preg_match('#^u/([^/]+)/(.+)$#', $photo, $matches)) {
        return Response::error('Invalid photo format. Expected: u/username/path/to/file', 400);
    }

    $username = $matches[1];
    $path = $matches[2];

    // Find user by username
    $userRepo = ORMHelper::getRepository(User::class);
    $user = $userRepo->findOne(['username' => $username]);

    if (!$user || !$user->isActive()) {
        return Response::error('User not found', 404);
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