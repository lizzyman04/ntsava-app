<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\SettingsController;

Flow::POST()->to(SettingsController::class, 'updateProfile');