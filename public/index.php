<?php

use Core\Request;
use Core\Response;
use Core\Router;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../bootstrap/helpers.php';

// Initialize Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create Router Instance
$request = new Request();
$response = new Response();
$router = new Router($request, $response);

// Load Routes
require_once __DIR__ . '/web.php';

// Resolve Route
$router->resolve();
