<?php

namespace App\Controllers;

class ExampleController
{
    public function hello()
    {
        header('Content-Type: application/json');

        // ✅ Define the payload
        $payload = [
            'type' => 'greeting',
            'data' => [
                'message' => 'Hello from API!',
                'timestamp' => time(),
            ]
        ];

        // ✅ Send to WebSocket server
        exec("php sockets/socket-updator.php " . escapeshellarg(json_encode($payload)));

        return json_encode(['message' => 'Hello from API!']);
    }

    public function hello2($value)
    {
        header('Content-Type: application/json');

        return json_encode([
            'message' => 'Hello from API!',
            'value' => $value
        ]);
    }
}
