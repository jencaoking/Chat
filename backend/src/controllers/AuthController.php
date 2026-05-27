<?php
namespace App\Controllers;

use App\Models\User;
use App\Utils\JWT;
use Slim\Psr7\Response;

class AuthController {
    public function register($request, $response, $args) {
        try {
            $data = $request->getParsedBody();
            
            if (!isset($data['username'], $data['email'], $data['password'])) {
                $response->getBody()->write(json_encode(['error' => '缺少必要字段']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            if (User::where('email', $data['email'])->exists()) {
                $response->getBody()->write(json_encode(['error' => '该邮箱已被注册']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            if (User::where('username', $data['username'])->exists()) {
                $response->getBody()->write(json_encode(['error' => '该用户名已被使用']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
            
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => 'user',
                'status' => 'online'
            ]);
            
            $token = JWT::encode(['user_id' => $user->id]);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status
                ]
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => '注册失败: ' . $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
    
    public function login($request, $response, $args) {
        $data = $request->getParsedBody();
        
        if (!isset($data['email'], $data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Missing required fields']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        $user = User::where('email', $data['email'])->first();
        
        if (!$user || !password_verify($data['password'], $user->password)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $user->status = 'online';
        $user->save();
        
        $token = JWT::encode(['user_id' => $user->id]);
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ]
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function getUser($request, $response, $args) {
        $user = $request->getAttribute('user');
        
        $response->getBody()->write(json_encode([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status
            ]
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
    
    public function logout($request, $response, $args) {
        $user = $request->getAttribute('user');
        $user->status = 'offline';
        $user->save();
        
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Logged out successfully']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}