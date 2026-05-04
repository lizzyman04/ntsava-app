<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePlansTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('plans', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('slug', 'string', ['limit' => 50])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('storage_limit_bytes', 'biginteger', ['signed' => false])
            ->addColumn('bandwidth_limit_bytes', 'biginteger', ['signed' => false])
            ->addColumn('max_file_size_bytes', 'biginteger', ['signed' => false, 'default' => 10485760])
            ->addColumn('allowed_mime_types', 'text', ['default' => '["jpg","jpeg","png","gif","webp"]'])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
            ->addColumn('currency', 'string', ['limit' => 3, 'default' => 'MZN'])
            ->addColumn('is_active', 'boolean', ['default' => true])
            ->addColumn('sort_order', 'integer', ['default' => 0])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['name'], ['unique' => true, 'name' => 'idx_name'])
            ->addIndex(['slug'], ['unique' => true, 'name' => 'idx_slug'])
            ->addIndex(['is_active'], ['name' => 'idx_is_active'])
            ->addIndex(['currency'], ['name' => 'idx_currency'])
            ->create();
    }
}
