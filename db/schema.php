#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cycle\Database\DatabaseManager;

function executeSchema(DatabaseManager $database, string $schemaFile): void
{
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: {$schemaFile}");
    }

    $sql = file_get_contents($schemaFile);

    // Remove comments
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    $successCount = 0;
    $errorCount = 0;

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $database->database()->execute($statement);
                echo "✅ Executed: " . substr(trim($statement), 0, 60) . "\n";
                $successCount++;
            } catch (Exception $e) {
                echo "❌ Failed: " . substr(trim($statement), 0, 60) . "\n";
                echo "   Error: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
    }

    echo "\n📊 Schema execution completed:\n";
    echo "   ✅ Successful: {$successCount}\n";
    echo "   ❌ Failed: {$errorCount}\n";
}

function checkExistingTables(DatabaseManager $database): array
{
    try {
        $tables = $database->database()->getTables();
        return array_map(fn($table) => $table->getName(), $tables);
    } catch (Exception $e) {
        return [];
    }
}

echo "🚀 Starting database schema setup...\n\n";

try {
    $database = require __DIR__ . '/connection.php';

    echo "📡 Checking database connection...\n";
    $database->database()->execute('SELECT 1');
    echo "✅ Database connection successful\n\n";

    echo "🔍 Checking existing tables...\n";
    $existingTables = checkExistingTables($database);

    if (!empty($existingTables)) {
        echo "📋 Existing tables found:\n";
        foreach ($existingTables as $table) {
            echo "   - {$table}\n";
        }
        echo "\n⚠️  Note: Some statements may fail if tables already exist\n\n";
    } else {
        echo "✅ No existing tables found\n\n";
    }

    echo "📝 Executing schema from config/schema.sql...\n\n";
    $schemaFile = __DIR__ . '/../config/schema.sql';
    executeSchema($database, $schemaFile);

    echo "\n🎉 Database schema setup completed!\n";

} catch (Exception $e) {
    echo "❌ Schema setup failed: " . $e->getMessage() . "\n";
    exit(1);
}