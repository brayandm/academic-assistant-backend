<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AiModel;
use App\Models\Policy;
use App\Models\Role;
use App\Models\TaskType;
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
            'name' => 'USER_MANAGEMENT',
        ]);

        $translationTaskManagementPolicy = Policy::factory()->create([
            'name' => 'TRANSLATION_TASK_MANAGEMENT',
        ]);

        $adminDashboardAccessPolicy = Policy::factory()->create([
            'name' => 'ADMIN_DASHBOARD_ACCESS',
        ]);

        $teacherDashboardAccessPolicy = Policy::factory()->create([
            'name' => 'TEACHER_DASHBOARD_ACCESS',
        ]);

        $aiAssistantDashboardAccessPolicy = Policy::factory()->create([
            'name' => 'AI_ASSISTANT_DASHBOARD_ACCESS',
        ]);

        // ROLES

        $studentRole = Role::factory()->create([
            'name' => 'STUDENT',
        ]);

        $teacherRole = Role::factory()->create([
            'name' => 'TEACHER',
        ]);

        $adminRole = Role::factory()->create([
            'name' => 'ADMIN',
        ]);

        // ATTACH POLICIES TO ROLES

        $teacherRole->policies()->sync([$translationTaskManagementPolicy->id, $teacherDashboardAccessPolicy->id, $aiAssistantDashboardAccessPolicy->id]);

        $adminRole->policies()->sync([$userManagementPolicy->id, $adminDashboardAccessPolicy->id]);

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

        // TASK TYPES

        $translationTaskType = TaskType::factory()->create([
            'name' => 'TRANSLATION',
        ]);

        $chatCompletionTaskType = TaskType::factory()->create([
            'name' => 'CHAT_COMPLETION',
        ]);

        $textToSpeechStandardTaskType = TaskType::factory()->create([
            'name' => 'TEXT_TO_SPEECH_STANDARD',
        ]);

        $textToSpeechNeuralTaskType = TaskType::factory()->create([
            'name' => 'TEXT_TO_SPEECH_NEURAL',
        ]);

        $speechToTextTaskType = TaskType::factory()->create([
            'name' => 'SPEECH_TO_TEXT',
        ]);

        // AI MODELS

        $gpt_3_5_turbo = AiModel::factory()->create([
            'name' => 'gpt-3.5-turbo',
            'option' => 'chat-completion',
            'usage_type' => 'tokens',
        ]);

        $awsPollyStandard = AiModel::factory()->create([
            'name' => 'aws-polly',
            'option' => 'standard-voice',
            'usage_type' => 'characters',
        ]);

        $awsPollyNeural = AiModel::factory()->create([
            'name' => 'aws-polly',
            'option' => 'neural-voice',
            'usage_type' => 'characters',
        ]);

        $awsTranscribe = AiModel::factory()->create([
            'name' => 'aws-transcribe',
            'option' => 'speech-to-text',
            'usage_type' => 'seconds',
        ]);

        // ATTACH AI MODELS TO TASK TYPES

        $translationTaskType->aiModels()->sync([$gpt_3_5_turbo->id]);

        $chatCompletionTaskType->aiModels()->sync([$gpt_3_5_turbo->id]);

        $textToSpeechStandardTaskType->aiModels()->sync([$awsPollyStandard->id]);

        $textToSpeechNeuralTaskType->aiModels()->sync([$awsPollyNeural->id]);

        $speechToTextTaskType->aiModels()->sync([$awsTranscribe->id]);

        // USER QUOTAS

        $student->quotas()->attach($gpt_3_5_turbo->id, [
            'quota' => 100,
        ]);

        $teacher->quotas()->attach($gpt_3_5_turbo->id, [
            'quota' => 1000,
        ]);

        $admin->quotas()->attach($gpt_3_5_turbo->id, [
            'quota' => 10000,
        ]);

        $admin->quotas()->attach($awsPollyStandard->id, [
            'quota' => 10000,
        ]);

        $admin->quotas()->attach($awsPollyNeural->id, [
            'quota' => 10000,
        ]);

        $admin->quotas()->attach($awsTranscribe->id, [
            'quota' => 60,
        ]);
    }
}
