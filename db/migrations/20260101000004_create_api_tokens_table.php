<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateApiTokensTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('api_tokens', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('token_hash', 'string', ['limit' => 255])
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('permissions', 'json')
            ->addColumn('last_used_at', 'timestamp', ['null' => true])
            ->addColumn('expires_at', 'timestamp', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['token_hash'], ['unique' => true, 'name' => 'idx_token_hash'])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['expires_at'], ['name' => 'idx_expires_at'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
