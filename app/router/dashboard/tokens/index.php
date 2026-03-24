<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\TokensController;

Flow::GET()->to(TokensController::class, 'index');