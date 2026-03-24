<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\FilesController;

Flow::DELETE()->do(function ($req) {
    $uuid = $req->param('uuid');
    $controller = new FilesController();
    return $controller->delete($uuid);
});