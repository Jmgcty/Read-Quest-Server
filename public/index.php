<?php

declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\Controllers\AuthController\LoginController;
use App\Controllers\AuthController\SignupController;
use App\Controllers\InstitutionController\InstitutionMemberController;

try {

    $app = AppFactory::create();

    // Parse body middleware
    $app->addBodyParsingMiddleware();

    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write('Server is running');
        return $response;
    });


    // Routing
    $app->post('/api/auth/login', [LoginController::class, 'login']);
    $app->post('/api/auth/signup', [SignupController::class, 'signup']);
    //
    $app->get('/api/institution/member/{id}', [InstitutionMemberController::class, 'getUserInstitution']);

    // Run the app
    $app->run();
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['message' => $e->getMessage()]);
}
