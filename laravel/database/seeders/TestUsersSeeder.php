<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'email_verified' => true,
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'abcd@gmail.com',
            'password' => Hash::make('aaaa1234'),
            'role' => 'user',
            'email_verified' => true,
        ]);
    }
}
