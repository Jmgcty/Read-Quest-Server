<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInstitutionMembersTable extends AbstractMigration
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
        $this->table('institution_members', ['id' => false, 'primary_key' => ['user_id', 'institution_id']])
            ->addColumn('user_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('institution_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('institutional_id', 'integer', ['limit' => 11, 'null' => false, 'signed' => false])
            ->addColumn('role', 'enum', ['values' => ['admin', 'student', 'teacher'], 'null' => true, 'default' => null])
            ->addColumn('is_valid_member', 'boolean', ['null' => true, 'default' => null])
            ->addTimestamps()

            ->addIndex(['user_id'], ['unique' => true])
            ->addIndex(['institution_id'], ['unique' => true])
            ->addIndex(['institutional_id'], ['unique' => true])
            ->addForeignKey('user_id', 'users', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addForeignKey('institution_id', 'institutions', ['id'], ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();
    }

    public function down(): void
    {
        $this->table('institution_members')->drop()->update();
    }
}
