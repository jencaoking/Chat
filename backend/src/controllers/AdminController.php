<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Message;
use Slim\Psr7\Response;

class AdminController {
    public function getUsers($request, $response, $args) {
        $users = User::orderBy('created_at', 'desc')->get();
        
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at
            ];
        }
        
        $response->getBody()->write(json_encode(['success' => true, 'data' => $result]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function deleteUser($request, $response, $args) {
        $userId = $args['id'];
        
        if ($userId == $request->getAttribute('user')->id) {
            $response->getBody()->write(json_encode(['error' => 'Cannot delete yourself']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $user = User::find($userId);
        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'User not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $user->delete();
        
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'User deleted']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function getMessages($request, $response, $args) {
        $messages = Message::with(['from', 'to'])->orderBy('created_at', 'desc')->get();
        
        $result = [];
        foreach ($messages as $msg) {
            $result[] = [
                'id' => $msg->id,
                'from' => [
                    'id' => $msg->from->id,
                    'username' => $msg->from->username
                ],
                'to' => [
                    'id' => $msg->to->id,
                    'username' => $msg->to->username
                ],
                'content' => $msg->content,
                'type' => $msg->type,
                'status' => $msg->status,
                'created_at' => $msg->created_at
            ];
        }
        
        $response->getBody()->write(json_encode(['success' => true, 'data' => $result]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function deleteMessage($request, $response, $args) {
        $messageId = $args['id'];
        
        $message = Message::find($messageId);
        if (!$message) {
            $response->getBody()->write(json_encode(['error' => 'Message not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $message->delete();
        
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Message deleted']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function getStats($request, $response, $args) {
        $userCount = User::count();
        $messageCount = Message::count();
        $onlineCount = User::where('status', 'online')->count();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'data' => [
                'total_users' => $userCount,
                'total_messages' => $messageCount,
                'online_users' => $onlineCount
            ]
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}