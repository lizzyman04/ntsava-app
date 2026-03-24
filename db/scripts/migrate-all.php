#!/usr/bin/env php
<?php

/**
 * Run all migrations in order
 * Usage: php db/scripts/migrate-all.php
 *        composer migrate
 */

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../core/connection.php';

$migrationsPath = base_path('db/migrations');

// Get all .sql files sorted by name
$files = glob($migrationsPath . '/*.sql');
sort($files);

if (empty($files)) {
    echo "❌ No migration files found in: {$migrationsPath}\n";
    exit(1);
}

echo "🚀 Running all migrations...\n\n";

try {
    $database = require __DIR__ . '/../core/connection.php';

    $totalSuccess = 0;
    $totalError = 0;

    foreach ($files as $file) {
        $filename = basename($file);
        echo "📝 Running: {$filename}\n";

        $sql = file_get_contents($file);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        $successCount = 0;
        $errorCount = 0;

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $database->database()->execute($statement);
                    echo "  ✅ " . substr($statement, 0, 50) . "\n";
                    $successCount++;
                } catch (Exception $e) {
                    echo "  ❌ Failed: " . substr($statement, 0, 50) . "\n";
                    echo "     Error: " . $e->getMessage() . "\n";
                    $errorCount++;
                }
            }
        }

        echo "  📊 {$filename}: {$successCount} OK, {$errorCount} Failed\n\n";
        $totalSuccess += $successCount;
        $totalError += $errorCount;
    }

    echo "🎉 All migrations completed!\n";
    echo "📊 Total: {$totalSuccess} OK, {$totalError} Failed\n";

} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}