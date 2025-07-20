<?php


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host = $_ENV['SOCKET_HOST'] ?? 'localhost';
$port = $_ENV['SOCKET_PORT'] ?? 8080;

$context = stream_context_create();
$socket = stream_socket_client("tcp://$host:$port", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);

if ($socket) {
    fwrite($socket, $argv[1] ?? 'no message');
    fclose($socket);
} else {
    echo "Failed to connect to WebSocket: $errstr ($errno)\n";
}
