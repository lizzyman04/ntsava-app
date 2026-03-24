<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\TokensController;

Flow::POST()->to(TokensController::class, 'create');