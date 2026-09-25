<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Administrator', 'email' => 'admin@example.com', 'username' => 'admin', 'role' => 'admin'],
            ['name' => 'Operator', 'email' => 'operator@example.com', 'username' => 'operator', 'role' => 'operator'],
        ] as $user) {
            User::updateOrCreate(['email' => $user['email']], [...$user, 'password' => 'password']);
        }
    }
}
