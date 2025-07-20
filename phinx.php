<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


return
    [
        'paths' => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/Database/migrations',
            'seeds' => '%%PHINX_CONFIG_DIR%%/Database/seeds'
        ],
        'environments' => [
            'default_migration_table' => 'phinxlog',
            'default_environment' => 'development',
            'production' => [
                'adapter' => $_ENV['DB_CONNECTION'],
                'host' =>  $_ENV['DB_HOST'],
                'name' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USERNAME'],
                'pass' => $_ENV['DB_PASSWORD'],
                'port' => $_ENV['DB_PORT'],
                'charset' => 'utf8',
            ],
            'development' => [
                'adapter' => $_ENV['DB_CONNECTION'],
                'host' =>  $_ENV['DB_HOST'],
                'name' => $_ENV['DB_NAME'],
                'user' => $_ENV['DB_USERNAME'],
                'pass' => $_ENV['DB_PASSWORD'],
                'port' => $_ENV['DB_PORT'],
                'charset' => 'utf8',
            ],
            'testing' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'testing_db',
                'user' => 'root',
                'pass' => '',
                'port' => '3306',
                'charset' => 'utf8',
            ]
        ],
        'version_order' => 'creation'
    ];
