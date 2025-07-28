<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateModulesTable extends AbstractMigration
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
        $this->table('modules')
            ->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('cover', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('file', 'string', ['default' => false])
            ->addColumn('status', 'boolean', ['default' => true])
            ->addColumn('uploaded_by', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('deleted_at', 'datetime', ['null' => true])
            ->addTimestamps()
            ->addForeignKey('uploaded_by', 'users', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }


    public function down(): void
    {
        $this->table('modules')->drop()->update();
    }
}
