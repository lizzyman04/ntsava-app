<?php
use Fluxor\Flow;
use Source\Controllers\Dashboard\SettingsController;

Flow::DELETE()->to(SettingsController::class, 'deleteAccount');