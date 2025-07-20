<?php

declare(strict_types=1);

namespace App\Controllers\AuthController;

require_once __DIR__ . '/../../../Database/bootstrap.php';

use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



class SignupController
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection();
    }

    public function signup(Request $request, Response $response): Response
    {

        $parseBody = $request->getParsedBody();
        $header = $request->getHeaders();
        //

        $name = $parseBody['name'] ?? null;
        $email = $parseBody['email'] ?? null;
        $password = $parseBody['password'] ?? null;
        $password_confirmation = $parseBody['password_confirmation'] ?? null;


        try {

            $this->connection->beginTransaction();
            $user = $this->connection->table('authentications')->where('email', $email)->first();

            if ($user) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'email already used',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'invalid email format',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if ($password !== $password_confirmation) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'confirmation passwords do not match',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if (strlen($password) < 6) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'password must be at least 6 characters',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $userID = $this->connection->table('users')->insertGetId([
                'name' => $name,
                'email' => $email,
            ]);

            $this->connection->table('authentications')->insert([
                'user_id' => $userID,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);


            $this->connection->commit();

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'signup successful',
            ]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            //
        } catch (Exception $e) {
            $this->connection->rollBack();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'server error',
                'error' => $e->getMessage(),
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
