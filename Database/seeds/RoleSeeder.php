<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Illuminate\Database\Capsule\Manager as DB;

class RoleSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        try {

            $conn = DB::connection();

            $roles = [
                [
                    'name' => 'admin',
                    'description' => 'A Administrator User',
                ],
                [
                    'name' => 'student',
                    'description' => 'A Student User',
                ],
                [
                    'name' => 'teacher',
                    'description' => 'A Teacher User',
                ]
            ];
            $conn->beginTransaction();

            foreach ($roles as $role) {
                $conn->table('roles')->insert($role);
            }

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
