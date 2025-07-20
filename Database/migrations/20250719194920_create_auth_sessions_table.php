<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAuthSessionsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up(): void
    {
        $this->table('auth_sessions')
            ->addColumn('auth_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('device', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('token', 'string', ['limit' => 255, 'null' => false])
            ->addIndex(['token'], ['unique' => true])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addForeignKey('auth_id', 'authentications', ['user_id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }

    public function down(): void
    {
        $this->table('auth_sessions')->drop()->update();
    }
}
