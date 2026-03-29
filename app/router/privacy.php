<?php

use Fluxor\Flow;
use Source\Controllers\PageController;

Flow::GET()->to(PageController::class, 'privacy');