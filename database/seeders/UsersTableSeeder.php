<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($users as $user) {
            // \App\Models\User::create($user);
            User::create($user);
        }
    }
}
