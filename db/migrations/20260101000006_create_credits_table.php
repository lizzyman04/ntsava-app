<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCreditsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('credits', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
            ->addColumn('currency', 'string', ['limit' => 3, 'default' => 'MZN'])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['currency'], ['name' => 'idx_currency'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
