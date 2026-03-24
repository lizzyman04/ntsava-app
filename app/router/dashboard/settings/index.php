<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\SettingsController;

Flow::GET()->to(SettingsController::class, 'index');