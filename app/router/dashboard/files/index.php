<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\FilesController;

Flow::GET()->to(FilesController::class, 'index');