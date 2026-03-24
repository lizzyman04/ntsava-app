<?php

use Fluxor\Flow;
use Fluxor\Response;

Flow::GET()->do(function ($req) {
    $key = $req->param('key');
    $cachePath = base_path('storage/cache/' . $key);

    if (!file_exists($cachePath)) {
        return Response::error('Cache file not found', 404);
    }

    $mimeType = mime_content_type($cachePath);

    header('Content-Type: ' . $mimeType);
    header('Cache-Control: public, max-age=31536000');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

    readfile($cachePath);
    exit;
});