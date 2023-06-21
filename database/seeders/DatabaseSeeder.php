<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Policy;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // POLICIES

        $userManagementPolicy = Policy::factory()->create([
            'name' => "USER_MANAGEMENT",
        ]);

        $translationTaskManagementPolicy = Policy::factory()->create([
            'name' => "TRANSLATION_TASK_MANAGEMENT",
        ]);

        $adminPortalAccessPolicy = Policy::factory()->create([
            'name' => "ADMIN_PORTAL_ACCESS",
        ]);

        $teacherPortalAccessPolicy = Policy::factory()->create([
            'name' => "TEACHER_PORTAL_ACCESS",
        ]);

        // ROLES

        $studentRole = Role::factory()->create([
            'name' => "STUDENT"
        ]);

        $teacherRole = Role::factory()->create([
            'name' => "TEACHER"
        ]);

        $adminRole = Role::factory()->create([
            'name' => "ADMIN"
        ]);

        // ATTACH POLICIES TO ROLES

        $teacherRole->policies()->sync([$translationTaskManagementPolicy->id, $teacherPortalAccessPolicy->id]);

        $adminRole->policies()->sync([$userManagementPolicy->id, $adminPortalAccessPolicy->id]);

        // USERS

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

        // ATTACH ROLES TO USERS

        $student->roles()->sync([$studentRole->id]);
        $teacher->roles()->sync([$studentRole->id, $teacherRole->id]);
        $admin->roles()->sync([$studentRole->id, $teacherRole->id, $adminRole->id]);
    }
}
