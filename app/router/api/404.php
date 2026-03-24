<?php

use Fluxor\Response;

return function($req) {
    return Response::error('API endpoint not found', 404);
};