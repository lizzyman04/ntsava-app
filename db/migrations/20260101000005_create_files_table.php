<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFilesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('files', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('uuid', 'char', ['limit' => 36])
            ->addColumn('storage_path', 'string', ['limit' => 500, 'null' => true])
            ->addColumn('original_name', 'string', ['limit' => 255])
            ->addColumn('size_bytes', 'biginteger', ['signed' => false])
            ->addColumn('mime_type', 'string', ['limit' => 100])
            ->addColumn('width', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('height', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('duration_seconds', 'integer', ['signed' => false, 'null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('deleted_at', 'timestamp', ['null' => true])
            ->addIndex(['uuid'], ['unique' => true, 'name' => 'idx_uuid'])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['storage_path'], ['name' => 'idx_storage_path', 'limit' => ['storage_path' => 191]])
            ->addIndex(['created_at'], ['name' => 'idx_created_at'])
            ->addIndex(['deleted_at'], ['name' => 'idx_deleted_at'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
