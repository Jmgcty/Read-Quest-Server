<?php

namespace App\Helpers;

use WebSocket\Client;

class WebSocketSender
{
    public static function send(string $message): void
    {
        $host = $_ENV['SOCKET_HOST'] ?? 'localhost';
        $port = $_ENV['SOCKET_PORT'] ?? 8000;

        try {
            $client = new Client("ws://$host:$port/live");
            $client->send($message);
            $client->close();
        } catch (\Exception $e) {
            error_log("WebSocket error: " . $e->getMessage());
        }
    }
}
