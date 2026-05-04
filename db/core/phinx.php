<?php

require_once __DIR__ . '/bootstrap.php';

$adapterMap = [
    'mysql'  => 'mysql',
    'pgsql'  => 'pgsql',
    'sqlite' => 'sqlite',
];

$connection = env('DB_CONNECTION', 'mysql');
$adapter    = $adapterMap[$connection] ?? 'mysql';

$connectionConfig = match ($adapter) {
    'sqlite' => [
        'adapter' => 'sqlite',
        'name'    => env('DB_DATABASE', 'ntsava_db'),
    ],
    default => [
        'adapter' => $adapter,
        'host'    => env('DB_HOST', '127.0.0.1'),
        'name'    => env('DB_DATABASE', 'ntsava_db'),
        'user'    => env('DB_USERNAME', 'root'),
        'pass'    => env('DB_PASSWORD', ''),
        'port'    => (int) env('DB_PORT', 3306),
        'charset' => 'utf8mb4',
    ],
};

return [
    'paths' => [
        'migrations' => 'db/migrations',
        'seeds'      => 'db/seeders',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'development'             => $connectionConfig,
        'production'              => $connectionConfig,
    ],
];
