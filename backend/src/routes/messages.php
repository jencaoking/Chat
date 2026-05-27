<?php
use Slim\App;
use App\Controllers\MessageController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $messageController = new MessageController();
    $authMiddleware = new AuthMiddleware();
    
    $app->get('/api/users', [$messageController, 'getUsers'])->add($authMiddleware);
    $app->get('/api/conversations', [$messageController, 'getConversations'])->add($authMiddleware);
    $app->get('/api/messages/{id}', [$messageController, 'getMessages'])->add($authMiddleware);
    $app->post('/api/messages', [$messageController, 'sendMessage'])->add($authMiddleware);
    $app->put('/api/messages/{id}/read', [$messageController, 'markAsRead'])->add($authMiddleware);
};