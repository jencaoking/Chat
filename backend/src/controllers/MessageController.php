<?php
namespace App\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Slim\Psr7\Response;

class MessageController {
    public function getUsers($request, $response, $args) {
        $user = $request->getAttribute('user');
        
        $users = User::where('id', '!=', $user->id)
            ->orderBy('username', 'asc')
            ->get();
        
        $result = [];
        foreach ($users as $u) {
            $result[] = [
                'id' => $u->id,
                'username' => $u->username,
                'email' => $u->email,
                'status' => $u->status
            ];
        }
        
        $response->getBody()->write(json_encode(['success' => true, 'data' => $result]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function getConversations($request, $response, $args) {
        $user = $request->getAttribute('user');
        
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with(['user1', 'user2', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $result = [];
        foreach ($conversations as $conv) {
            $otherUser = $conv->user1_id == $user->id ? $conv->user2 : $conv->user1;
            $result[] = [
                'id' => $conv->id,
                'user' => [
                    'id' => $otherUser->id,
                    'username' => $otherUser->username,
                    'email' => $otherUser->email,
                    'status' => $otherUser->status
                ],
                'last_message' => $conv->lastMessage ? [
                    'id' => $conv->lastMessage->id,
                    'content' => $conv->lastMessage->content,
                    'created_at' => $conv->lastMessage->created_at,
                    'is_me' => $conv->lastMessage->from_id == $user->id
                ] : null,
                'updated_at' => $conv->updated_at
            ];
        }
        
        $response->getBody()->write(json_encode(['success' => true, 'data' => $result]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function getMessages($request, $response, $args) {
        $user = $request->getAttribute('user');
        $conversationId = $args['id'];
        
        $conversation = Conversation::find($conversationId);
        if (!$conversation || ($conversation->user1_id != $user->id && $conversation->user2_id != $user->id)) {
            $response->getBody()->write(json_encode(['error' => 'Conversation not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $messages = Message::where('conversation_id', $conversationId)
            ->with(['from', 'to'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $result = [];
        foreach ($messages as $msg) {
            $result[] = [
                'id' => $msg->id,
                'from_id' => $msg->from_id,
                'to_id' => $msg->to_id,
                'content' => $msg->content,
                'type' => $msg->type,
                'status' => $msg->status,
                'created_at' => $msg->created_at,
                'is_me' => $msg->from_id == $user->id
            ];
        }
        
        $response->getBody()->write(json_encode(['success' => true, 'data' => $result]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function sendMessage($request, $response, $args) {
        $user = $request->getAttribute('user');
        $data = $request->getParsedBody();
        
        if (!isset($data['to_id'], $data['content'])) {
            $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        if ($data['to_id'] == $user->id) {
            $response->getBody()->write(json_encode(['error' => 'Cannot send message to yourself']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $recipient = User::find($data['to_id']);
        if (!$recipient) {
            $response->getBody()->write(json_encode(['error' => 'Recipient not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $conversation = Conversation::where(function ($query) use ($user, $recipient) {
            $query->where('user1_id', $user->id)->where('user2_id', $recipient->id);
        })->orWhere(function ($query) use ($user, $recipient) {
            $query->where('user1_id', $recipient->id)->where('user2_id', $user->id);
        })->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user->id,
                'user2_id' => $recipient->id
            ]);
        }
        
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'from_id' => $user->id,
            'to_id' => $recipient->id,
            'content' => $data['content'],
            'type' => $data['type'] ?? 'text',
            'status' => 'sent'
        ]);
        
        $conversation->last_message_id = $message->id;
        $conversation->save();
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'from_id' => $message->from_id,
                'to_id' => $message->to_id,
                'content' => $message->content,
                'type' => $message->type,
                'status' => $message->status,
                'created_at' => $message->created_at
            ]
        ]));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }
    
    public function markAsRead($request, $response, $args) {
        $user = $request->getAttribute('user');
        $messageId = $args['id'];
        
        $message = Message::find($messageId);
        if (!$message || $message->to_id != $user->id) {
            $response->getBody()->write(json_encode(['error' => 'Message not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        
        $message->status = 'read';
        $message->save();
        
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}