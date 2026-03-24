#!/usr/bin/env php
<?php

/**
 * Run a specific migration
 * Usage: php db/scripts/migrate.php 0004_create_api_tokens_table.sql
 *        composer migrate:run 0004_create_api_tokens_table.sql
 */

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/connection.php';

$migrationFile = $argv[1] ?? null;

if (!$migrationFile) {
    echo "❌ Usage: php db/scripts/migrate.php <migration_file>\n";
    echo "   Example: php db/scripts/migrate.php 0004_create_api_tokens_table.sql\n";
    exit(1);
}

$migrationsPath = base_path('db/migrations');
$filePath = $migrationsPath . '/' . $migrationFile;

if (!file_exists($filePath)) {
    echo "❌ Migration file not found: {$filePath}\n";
    exit(1);
}

echo "🚀 Running migration: {$migrationFile}\n\n";

try {
    $database = require __DIR__ . '/../core/connection.php';

    $sql = file_get_contents($filePath);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $database->database()->execute($statement);
                echo "✅ " . substr($statement, 0, 60) . "\n";
                $successCount++;
            } catch (Exception $e) {
                echo "❌ Failed: " . substr($statement, 0, 60) . "\n";
                echo "   Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }

    echo "\n📊 Migration completed:\n";
    echo "   ✅ Successful: {$successCount}\n";
    echo "   ❌ Failed: {$errorCount}\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}