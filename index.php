<?php

require 'vendor/autoload.php';
require 'routes.php';

use FastRoute\Dispatcher;
use function FastRoute\simpleDispatcher;

// Access JWT_SECRET environment variable

// Debugging the variable
// Define dispatcher
$dispatcher = simpleDispatcher(require 'routes.php');

// Get request method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?'); // Remove query string

// Dispatch route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "404 Not Found";
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo "405 Method Not Allowed";
        break;
    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        if (class_exists($class) && method_exists($class, $method)) {
            $controller = new $class();
            $controller->$method($vars);
        } else {
            echo "Controller or method not found!";
        }
        break;
}


$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
$whoops->register();
