<?php

declare(strict_types=1);

namespace App\Controllers\ModuleController;

require_once __DIR__ . '/../../../Database/bootstrap.php';
require_once __DIR__ . '/../../../Utils/enums/boolean.php';

use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;



class ReadingModuleController
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection();
    }

    public function uploadModule(Request $request, Response $response): Response
    {
        $coverPath = null;
        $docPath = null;

        try {
            $directory = dirname(__DIR__, 2) . '/../public/uploads';

            $parsedBody = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            $name = $parsedBody['name'] ?? null;
            $description = $parsedBody['description'] ?? null;
            $uploadedBy = $parsedBody['uploaded_by'] ?? null;

            $coverFile = $uploadedFiles['cover'] ?? null;
            $docFile = $uploadedFiles['file'] ?? null;

            if (!$coverFile || !$docFile) {
                $response->getBody()->write(json_encode([
                    'status' => 'failed',
                    'message' => 'Please provide both cover and document files.'
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            // Generate unique filenames
            $coverFilename = uniqid('cover_') . '_' . $coverFile->getClientFilename();
            $docFilename = uniqid('file_') . '_' . $docFile->getClientFilename();

            $coverPath = $directory . '/' . $coverFilename;
            $docPath = $directory . '/' . $docFilename;

            // Save files to disk
            $coverFile->moveTo($coverPath);
            $docFile->moveTo($docPath);

            $this->connection->beginTransaction();

            $this->connection->table('modules')->insert([
                'name' => $name,
                'description' => $description,
                'cover' => $coverFilename,
                'file' => $docFilename,
                'uploaded_by' => $uploadedBy
            ]);

            $this->connection->commit();

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Files uploaded successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->connection->rollBack();

            // Delete files if they were already uploaded
            if ($coverPath && file_exists($coverPath)) {
                unlink($coverPath);
            }
            if ($docPath && file_exists($docPath)) {
                unlink($docPath);
            }

            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }


    public function getModulesByUploader(Request $request, Response $response): Response
    {
        try {
            $userID = $request->getAttribute('id');
            $this->connection->beginTransaction();
            $modules = $this->connection->table('modules')->where('uploaded_by', '=', $userID)->get();
            $this->connection->commit();
            if (empty($modules)) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'no modules found',
                    'data' => $modules
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Modules fetched successfully',
                'data' => $modules
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->connection->rollBack();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getAllBook(Request $request, Response $response): Response
    {
        try {
            $this->connection->beginTransaction();
            $modules = $this->connection->table('modules')->get();
            $this->connection->commit();
            if (empty($modules)) {
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'message' => 'no modules found',
                    'data' => $modules
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Modules fetched successfully',
                'data' => $modules
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->connection->rollBack();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Server error',
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}
