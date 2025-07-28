<?php

declare(strict_types=1);

namespace App\Controllers\AuthController;

require_once __DIR__ . '/../../../Database/bootstrap.php';

use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



class LoginController
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection();
    }

    public function login(Request $request, Response $response): Response
    {


        $parseBody = $request->getParsedBody();
        $header = $request->getHeaders();
        //
        $email = $parseBody['email'] ?? null;
        $password = $parseBody['password'] ?? null;

        if (!$email || !$password) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'email and password required',
                'errors' => [
                    'email' => 'email required',
                    'password' => 'password required',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'invalid email format',
                'errors' => [
                    'email' => 'invalid email format',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {


            $this->connection->beginTransaction();
            $user = $this->connection->table('authentications')->where('email', strtolower($email))->first();

            if (!$user) {
                $response->getBody()->write(json_encode([
                    'status' => 'failed',
                    'message' => 'email not found',
                    'errors' => [
                        'email' => 'your email isn\'t connected to an account',
                    ]
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if (!password_verify($password, $user->password)) {
                $response->getBody()->write(json_encode([
                    'status' => 'failed',
                    'message' => 'incorrect password',
                    'errors' => [
                        'password' => 'your password is incorrect',
                    ]
                ]));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            $user_agent = $header['user-agent'][0] ?? null;
            $session_token = bin2hex(random_bytes(24));

            $this->connection->table('auth_sessions')->insert([
                'auth_id' => $user->user_id,
                'device' => $user_agent,
                'token' => $session_token,
            ]);

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'login successfully',
                'data' => [
                    'user_id' => $user->user_id,
                    'session_token' => $session_token,
                ]
            ]));

            $this->connection->commit();
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            //
        } catch (Exception $e) {
            $this->connection->rollBack();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'internal server error',
                'error' => $e->getMessage(),
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }


    public function logout(Request $request, Response $response): Response
    {

        $getBody = $request->getParsedBody();
        $session = $getBody['session_token'] ?? null;

        try {

            $this->connection->beginTransaction();
            $this->connection->table('auth_sessions')->where('token', '=', $session)->delete();
            $this->connection->commit();
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'logout successfully',
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            //
        } catch (Exception $e) {
            $this->connection->rollBack();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'internal server error',
                'error' => $e->getMessage(),
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
