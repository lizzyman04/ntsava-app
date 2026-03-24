<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\CreditsController;

Flow::POST()->do(function ($req) {
    $planSlug = $req->param('planSlug');
    $controller = new CreditsController();
    return $controller->upgrade($planSlug);
});