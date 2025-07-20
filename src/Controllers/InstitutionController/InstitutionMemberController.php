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
                'status' => 'error',
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
                    'status' => 'error',
                    'message' => 'user has no institution',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if ($institutionMember->is_valid_member === null) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'user is under review',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            if ($institutionMember->is_valid_member === BooleanValue::False->value) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'user is rejected',
                ]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'successfully fetched',
                'data' => $institutionMember,
            ]));

            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');

            $this->connection->commit();
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
