<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('uuid', 'char', ['limit' => 36])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('username', 'string', ['limit' => 50])
            ->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('password_hash', 'string', ['limit' => 255])
            ->addColumn('plan_id', 'integer', ['signed' => false])
            ->addColumn('storage_limit_bytes', 'biginteger', ['signed' => false])
            ->addColumn('storage_used_bytes', 'biginteger', ['signed' => false, 'default' => 0])
            ->addColumn('bandwidth_limit_bytes', 'biginteger', ['signed' => false])
            ->addColumn('bandwidth_used_bytes', 'biginteger', ['signed' => false, 'default' => 0])
            ->addColumn('bandwidth_reset_month', 'integer', ['signed' => false])
            ->addColumn('status', 'enum', ['values' => ['active', 'suspended', 'deleted'], 'default' => 'active'])
            ->addColumn('suspended_reason', 'text', ['null' => true])
            ->addColumn('suspended_at', 'timestamp', ['null' => true])
            ->addColumn('last_login_at', 'timestamp', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['uuid'], ['unique' => true, 'name' => 'idx_uuid'])
            ->addIndex(['email'], ['unique' => true, 'name' => 'idx_email'])
            ->addIndex(['username'], ['unique' => true, 'name' => 'idx_username'])
            ->addIndex(['plan_id'], ['name' => 'idx_plan_id'])
            ->addIndex(['status'], ['name' => 'idx_status'])
            ->addForeignKey('plan_id', 'plans', 'id', ['delete' => 'RESTRICT', 'update' => 'NO_ACTION'])
            ->create();
    }
}
