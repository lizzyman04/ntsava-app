<?php

use Fluxor\Flow;
use Fluxor\Response;
use Source\Models\User;
use App\Core\ORMHelper;
use Source\Services\ResizeService;

Flow::cors([
    'allowed_origins' => ['*'],
    'allowed_methods' => ['GET', 'OPTIONS'],
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

Flow::GET()->do(function ($req) {
    $photo = $req->input('photo');

    if (!$photo || !preg_match('#^u/([^/]+)/(.+)$#', $photo, $matches)) {
        return Response::error('Invalid photo format. Expected: u/username/path/to/file', 400);
    }

    $username = $matches[1];
    $path = $matches[2];

    $user = ORMHelper::getRepository(User::class)->findOne(['username' => $username]);
    if (!$user || !$user->isActive()) {
        return Response::error('User not found', 404);
    }

    $params = array_filter([
        'w' => $req->input('w'),
        'h' => $req->input('h'),
        'fit' => $req->input('fit'),
        'crop' => $req->input('crop'),
        'format' => $req->input('format'),
        'q' => $req->input('q'),
        'filter' => $req->input('filter'),
        'blur' => $req->input('blur'),
        'brightness' => $req->input('brightness'),
        'contrast' => $req->input('contrast'),
        'sharpen' => $req->input('sharpen'),
        'smooth' => $req->input('smooth'),
        'pixelate' => $req->input('pixelate'),
        'rotate' => $req->input('rotate'),
        'flip' => $req->input('flip'),
        'bg' => $req->input('bg'),
        'watermark' => $req->input('watermark'),
        'auto_orient' => $req->input('auto_orient')
    ], fn($v) => $v !== null && $v !== '');

    $result = (new ResizeService())->process($user, $path, $params);

    if (!$result['success']) {
        return Response::error($result['error'], 400);
    }

    if ($req->input('return') === 'json') {
        return Response::json([
            'success' => true,
            'url' => $result['url'],
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
            'size' => $result['size'] ?? null,
            'cached' => $result['cached'] ?? false
        ]);
    }

    return Response::redirect($result['url']);
});