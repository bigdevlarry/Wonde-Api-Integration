<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin users
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Verified teacher users
        $verifiedTeachers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Alice Brown',
                'email' => 'alice.brown@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($verifiedTeachers as $teacher) {
            User::firstOrCreate(
                ['email' => $teacher['email']],
                $teacher
            );
        }

        // Unverified users (for testing verification requirements)
        $unverifiedUsers = [
            [
                'name' => 'Unverified User 1',
                'email' => 'unverified1@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
            [
                'name' => 'Unverified User 2',
                'email' => 'unverified2@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => null,
            ],
        ];

        foreach ($unverifiedUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        // Test users with different roles/scenarios
        $testUsers = [
            [
                'name' => 'Test Teacher',
                'email' => 'test.teacher@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Test Admin',
                'email' => 'test.admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Test User',
                'email' => 'test.user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($testUsers as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
