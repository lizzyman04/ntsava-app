<?php

use Fluxor\Flow;
use Source\Controllers\PageController;

Flow::GET()->to(PageController::class, 'contact');
Flow::POST()->to(PageController::class, 'sendContact');