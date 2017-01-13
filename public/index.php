<?php 
use \controllers\UserController as UserController;
use \controllers\AuthController as AuthController;

require '../vendor/autoload.php';
spl_autoload_register(function ($name) {
	require $name . '.php';
});

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$conf = new \Slim\Container($configuration);

$app = new \Slim\App($configuration);
$userController = new UserController();
$authController = new AuthController();
$userController->bindController($app);
$authController->bindController($app);
$app->run();