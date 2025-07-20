<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
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
        $this->table('users')
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('phone', 'string', ['limit' => 30, 'null' => true])
            ->addColumn('address', 'text', ['limit' => 255, 'null' => true])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addIndex(['phone', 'email'], ['unique' => true])
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $this->table('users')->drop()->update();
    }
}
