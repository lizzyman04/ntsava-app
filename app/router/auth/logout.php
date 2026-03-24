<?php
use Fluxor\Flow;
use Source\Controllers\AuthController;

Flow::GET()->to(AuthController::class, 'logout');