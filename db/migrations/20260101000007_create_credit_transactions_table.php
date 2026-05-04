<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCreditTransactionsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('credit_transactions', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'biginteger', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'biginteger', ['signed' => false])
            ->addColumn('type', 'enum', ['values' => ['purchase', 'upgrade_plan', 'downgrade_plan', 'admin_add', 'admin_remove']])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('currency', 'string', ['limit' => 3, 'default' => 'MZN'])
            ->addColumn('balance_after', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('description', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('reference_id', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('metadata', 'json', ['null' => true])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['user_id'], ['name' => 'idx_user_id'])
            ->addIndex(['type'], ['name' => 'idx_type'])
            ->addIndex(['created_at'], ['name' => 'idx_created_at'])
            ->addIndex(['currency'], ['name' => 'idx_currency'])
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->create();
    }
}
