<?php

declare(strict_types=1);

namespace App\Controllers\InstitutionController;

require_once __DIR__ . '/../../../Database/bootstrap.php';
require_once __DIR__ . '/../../../Utils/enums/boolean.php';

use BooleanValue;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



class InstitutionMemberController
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection();
    }

    public function getUserInstitution(Request $request, Response $response): Response
    {

        $param = $request->getAttribute('id');

        //
        $user_id = null;
        $institutionMember = null;
        $user_id = $param ?? null;

        if (empty($user_id)) {

            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'user id is required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }


        try {
            $this->connection->beginTransaction();
            $institutionMember = $this->connection->table('institution_members')
                ->where('user_id', '=', $user_id)->first();

            if (empty($institutionMember)) {
                $response->getBody()->write(json_encode([
                    'status' => 'failed',
                    'message' => 'user has no institution',
                    'data' => $institutionMember
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $institutionKey = $this->connection->table('institution_keys')->where('role', '=', $institutionMember->role)->first();

            if ($institutionMember->is_valid_member === null) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'user is under review',
                    'key' => $institutionKey->key,
                    'data' => $institutionMember
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if ($institutionMember->is_valid_member === BooleanValue::False->value) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'user is rejected',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $this->connection->commit();
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'successfully fetched',
                'data' => $institutionMember,
            ]));

            exec("php sockets/updator.php '" . escapeshellarg("user_id=$user_id") . "'");
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


    public function joinInstitution(Request $request, Response $response): Response
    {

        $parseBody = $request->getParsedBody();

        $user_id = $parseBody['user_id'] ?? null;
        $institutional_id = $parseBody['institutional_id'] ?? null;
        $key = $parseBody['key'] ?? null;


        if (empty($user_id)) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'user id is required',
                'error' => [
                    'user_id' => 'User id is required.',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (empty($institutional_id)) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'institutional id is required',
                'error' => [
                    'institutional_id' => 'Institution id is required.',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (empty($key)) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'key is required',
                'error' => [
                    'key' => 'Key is required.',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }




        try {
            $this->connection->beginTransaction();
            $institutionKey = null;

            $institutionKey = $this->connection->table('institution_keys')
                ->where('key', '=', $key)
                ->first();

            if (empty($institutionKey)) {
                $response->getBody()->write(json_encode([
                    'status' => 'failed',
                    'message' => 'invalid key',
                    'error' => [
                        'key' => 'Key does not exist.',
                    ]
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $this->connection->table('institution_members')->insert([
                'user_id' => $user_id,
                'institution_id' => $institutionKey->institution_id,
                'institutional_id' => $institutional_id,
                'role' => $institutionKey->role,
            ]);

            $this->connection->commit();

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'successfully joined',
                'data' => [
                    'user_id' => $user_id,
                    'institutional_id' => $institutional_id,
                    'key' => $institutionKey->key,
                    'role' => $institutionKey->role
                ],
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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


    public function cancelJoinInstitution(Request $request, Response $response): Response
    {
        $parseBody = $request->getParsedBody();

        $user_id = $parseBody['user_id'] ?? null;

        if (empty($user_id)) {
            $response->getBody()->write(json_encode([
                'status' => 'failed',
                'message' => 'user id is required',
                'error' => [
                    'user_id' => 'User id is required.',
                ]
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            $this->connection->beginTransaction();

            $this->connection->table('institution_members')
                ->where('user_id', '=', $user_id)
                ->delete();

            $this->connection->commit();

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'successfully cancelled',
                'data' => [
                    'user_id' => $user_id,
                ],
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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
