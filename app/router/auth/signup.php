<?php
use Fluxor\Flow;
use Source\Controllers\AuthController;

Flow::GET()->to(AuthController::class, 'showSignup');
Flow::POST()->to(AuthController::class, 'signup');