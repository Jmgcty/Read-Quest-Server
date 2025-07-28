<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInstitutionKeysTable extends AbstractMigration
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
        $this->table('institution_keys')
            ->addColumn('institution_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('key', 'string', ['limit' => 300, 'null' => false])
            ->addColumn('role', 'enum', ['values' => ['admin',  'student', 'teacher'], 'null' => false])
            ->addIndex(['key'], ['unique' => true])
            ->addForeignKey('institution_id', 'institutions', ['id'], ['delete' => 'NO_ACTION', 'update' => 'NO_ACTION'])
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $this->table('institution_keys')->drop()->update();
    }
}
