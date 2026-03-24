<?php
use Fluxor\Flow;
use Source\Controllers\ContactController;

Flow::GET()->to(ContactController::class, 'index');
Flow::POST()->to(ContactController::class, 'send');