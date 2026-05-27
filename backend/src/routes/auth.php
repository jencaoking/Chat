<?php
use Slim\App;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $authController = new AuthController();
    $authMiddleware = new AuthMiddleware();
    
    $app->post('/api/auth/register', [$authController, 'register']);
    $app->post('/api/auth/login', [$authController, 'login']);
    $app->get('/api/auth/user', [$authController, 'getUser'])->add($authMiddleware);
    $app->post('/api/auth/logout', [$authController, 'logout'])->add($authMiddleware);
};