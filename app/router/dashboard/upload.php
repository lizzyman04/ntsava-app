<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\UploadController;

Flow::GET()->to(UploadController::class, 'index');
Flow::POST()->to(UploadController::class, 'process');