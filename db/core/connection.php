<?php

/**
 * Database Connection
 */

require_once __DIR__ . '/bootstrap.php';

use Cycle\Database;
use Cycle\Database\Config;

$dbConfig = new Config\DatabaseConfig([
    'default' => 'default',
    'databases' => [
        'default' => ['connection' => env('DB_CONNECTION', 'mysql')]
    ],
    'connections' => [
        'mysql' => new Config\MySQLDriverConfig(
            connection: new Config\MySQL\TcpConnectionConfig(
                database: env('DB_DATABASE', 'ntsava_db'),
                host: env('DB_HOST', 'localhost'),
                port: (int) env('DB_PORT', 3306),
                user: env('DB_USERNAME', 'root'),
                password: env('DB_PASSWORD', '')
            ),
            queryCache: true
        ),
        'sqlite' => new Config\SQLiteDriverConfig(
            connection: new Config\SQLite\FileConnectionConfig(
                database: env('DB_DATABASE', base_path('db/database.sqlite'))
            )
        )
    ]
]);

return new Database\DatabaseManager($dbConfig);