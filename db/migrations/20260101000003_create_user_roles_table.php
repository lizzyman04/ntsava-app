<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserRolesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('user_roles', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('role', 'enum', ['values' => ['admin', 'user', 'moderator']])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['user_id', 'role'], ['unique' => true, 'name' => 'unique_user_role'])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['role'], ['name' => 'idx_role'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
