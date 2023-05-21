<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $student = User::factory()->create([
            'name' => 'Student',
            'email' => 'student@example.com',
        ]);

        $teacher = User::factory()->create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        foreach(['Student', 'Teacher', 'Admin'] as $role) {
            Role::factory()->create([
                'name' => $role,
            ]);
        }

        $student->roles()->sync([1]);
        $teacher->roles()->sync([1, 2]);
        $admin->roles()->sync([1, 2, 3]);
    }
}
