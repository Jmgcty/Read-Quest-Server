<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Illuminate\Database\Capsule\Manager as DB;

class InstitutionSeeder extends AbstractSeed
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

            $institutions = [
                'name' => 'Institution 1',
                'description' => 'Description of Institution 1',
            ];

            $conn->beginTransaction();
            $conn->table('institutions')->insert($institutions);
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }

        // KEYS
        try {

            $conn = DB::connection();

            $institutions = [
                [
                    'institution_id' => 1,
                    'role' => 'student',
                    'key' => 'student_key',
                ],
                [
                    'institution_id' => 1,
                    'role' => 'teacher',
                    'key' => 'teacher_key',
                ],
                [
                    'institution_id' => 1,
                    'role' => 'admin',
                    'key' => 'admin_key',
                ],
            ];

            $conn->beginTransaction();
            foreach ($institutions as $institution) {
                $conn->table('institution_keys')->insert($institution);
            }
            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
