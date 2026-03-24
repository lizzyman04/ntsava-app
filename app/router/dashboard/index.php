<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\HomeController;

Flow::GET()->to(HomeController::class, 'index');