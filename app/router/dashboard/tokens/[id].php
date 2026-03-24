<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\TokensController;

Flow::DELETE()->do(function($req) {
    $id = $req->param('id');
    $controller = new TokensController();
    return $controller->revoke($id);
});