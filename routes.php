<?php

require_once __DIR__ . '/controllers/Controller.php'; // Use require_once

use FastRoute\RouteCollector;
use App\Controllers\Controller;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/api/uuid', [Controller::class, 'generateUuid']);
    $r->addRoute('POST', '/api/login', [Controller::class, 'login']);
    $r->addRoute('POST', '/api/protected', [Controller::class, 'protected']);
};
