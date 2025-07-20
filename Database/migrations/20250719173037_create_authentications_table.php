<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAuthenticationsTable extends AbstractMigration
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
        $this->table('authentications', ['id' => false, 'primary_key' => ['user_id']])
            ->addColumn('user_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('verified_at', 'datetime', ['null' => true])

            ->addIndex('email', ['unique' => true])
            ->addForeignKey('user_id', 'users', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $this->table('authentications')->drop()->update();
    }
}
