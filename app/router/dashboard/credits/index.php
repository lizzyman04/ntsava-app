<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\CreditsController;

Flow::GET()->to(CreditsController::class, 'index');