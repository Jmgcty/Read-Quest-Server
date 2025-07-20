<?php

declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';

use Phinx\Seed\AbstractSeed;
use Illuminate\Database\Capsule\Manager as DB;

class UserSeeder extends AbstractSeed
{
    public function run(): void
    {


        try {
            $conn = DB::connection();
            $user = [
                'name' => 'John Doe',
                'email' => 'tctt4@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St, Anytown, USA',
            ];
            $conn->beginTransaction();

            $userId = $conn->table('users')->insertGetId($user);
            $conn->table('authentications')->insert([
                'user_id' => $userId,
                'email' => $user['email'],
                'password' => password_hash('password', PASSWORD_DEFAULT),
            ]);

            $conn->commit();
        } catch (\Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
