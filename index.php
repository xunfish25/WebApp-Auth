<?php
// development helpers
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// load core framework
require __DIR__ . '/core/BaseController.php';
require __DIR__ . '/core/Router.php';
require __DIR__ . '/core/helpers.php';
require __DIR__ . '/core/exceptions.php';

// load configuration and models/controllers
require __DIR__ . '/app/config/db.php';
require __DIR__ . '/app/models/user.php';
require __DIR__ . '/app/controllers/AuthController.php';

$auth = new AuthController($conn);
$router = new Router($auth);
$router->dispatch();
?>