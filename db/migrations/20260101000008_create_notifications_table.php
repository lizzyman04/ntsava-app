<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateNotificationsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('notifications', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('type', 'enum', ['values' => ['system', 'plan_expiring', 'storage_warning', 'bandwidth_warning', 'upgrade_request', 'payment_received', 'suspended']])
            ->addColumn('title', 'string', ['limit' => 255])
            ->addColumn('message', 'text')
            ->addColumn('is_read', 'boolean', ['default' => false])
            ->addColumn('metadata', 'json', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('read_at', 'timestamp', ['null' => true])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['is_read'], ['name' => 'idx_is_read'])
            ->addIndex(['type'], ['name' => 'idx_type'])
            ->addIndex(['created_at'], ['name' => 'idx_created_at'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
