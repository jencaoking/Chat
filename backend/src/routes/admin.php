<?php
use Slim\App;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;

return function (App $app) {
    $adminController = new AdminController();
    $authMiddleware = new AuthMiddleware();
    $adminMiddleware = new AdminMiddleware();
    
    $app->get('/api/admin/users', [$adminController, 'getUsers'])->add($adminMiddleware)->add($authMiddleware);
    $app->delete('/api/admin/users/{id}', [$adminController, 'deleteUser'])->add($adminMiddleware)->add($authMiddleware);
    $app->get('/api/admin/messages', [$adminController, 'getMessages'])->add($adminMiddleware)->add($authMiddleware);
    $app->delete('/api/admin/messages/{id}', [$adminController, 'deleteMessage'])->add($adminMiddleware)->add($authMiddleware);
    $app->get('/api/admin/stats', [$adminController, 'getStats'])->add($adminMiddleware)->add($authMiddleware);
};