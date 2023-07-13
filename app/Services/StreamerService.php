<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\AiModel;
use App\Models\MachineLearningTask;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StreamerService
{
    public function accessControl(string $accessToken, string $taskTypeName)
    {
        $tokenHash = hash('sha256', explode('|', $accessToken)[1]);

        $token = DB::table('personal_access_tokens')->where('token', $tokenHash)->first();

        if (! $token) {
            return null;
        }

        $user = User::find($token->tokenable_id);

        if (! $user) {
            return null;
        }

        $taskType = TaskType::where('name', $taskTypeName)->first();

        if (! $taskType) {
            throw new AppException('Error in Engine Service', 'Task Type not found');
        }

        if (! $taskType->userHasQuota($user)) {
            return null;
        }

        return $user;
    }

    public function createTask(string $taskId, string $taskTypeName, string $taskStatus, int $userId, string $inputType, string $input, string $resultType, string $result, array $ai_models)
    {
        $taskType = TaskType::where('name', $taskTypeName)->first();

        if (! $taskType) {
            throw new AppException('Error in Engine Service', 'Task Type not found');
        }

        $machineLearningTask = MachineLearningTask::updateOrCreate([
            'task_id' => $taskId,
        ],
            [
                'task_type_id' => $taskType->id,
                'task_status' => $taskStatus,
                'user_id' => $userId,
                'input_type' => $inputType,
                'input' => $input,
                'result_type' => $resultType,
                'result' => $result,
            ]);

        $user = User::find($userId);

        foreach ($ai_models as $ai_model) {

            $aiModel = AiModel::firstOrCreate([
                'name' => $ai_model['name'],
                'option' => $ai_model['option'],
                'usage_type' => $ai_model['usage_type'],
            ]);

            $machineLearningTask->aiModels()->attach($aiModel->id, ['usage' => $ai_model['usage']]);

            $quota = $user->quotas()->where('ai_model_id', $aiModel->id)->first();

            $user->quotas()->updateExistingPivot($aiModel->id, ['quota' => $quota->pivot->quota - $ai_model['usage']]);

            $quota->save();
        }
    }
}
