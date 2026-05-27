<?php
namespace App\Middleware;

use App\Models\User;
use App\Utils\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $token = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $token);
        
        $decoded = JWT::decode($token);
        if (!$decoded) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $user = User::find($decoded->user_id);
        if (!$user) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'User not found']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $request = $request->withAttribute('user', $user);
        return $handler->handle($request);
    }
}