<?php

namespace App\Services;

use App\Exceptions\AppException;
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
            return false;
        }

        $user = User::find($token->tokenable_id);

        if (! $user) {
            return false;
        }

        $taskType = TaskType::where('name', $taskTypeName)->first();

        if (! $taskType) {
            throw new AppException('Error in Engine Service', 'Task Type not found');
        }

        return $taskType->userHasQuota($user);
    }

    public function createTask(string $taskId, string $taskTypeName, string $taskStatus, int $userId, string $inputType, string $input, string $resultType, string $result)
    {
        $taskType = TaskType::where('name', $taskTypeName)->first();

        if (! $taskType) {
            throw new AppException('Error in Engine Service', 'Task Type not found');
        }

        MachineLearningTask::create([
            'task_id' => $taskId,
            'task_type_id' => $taskType->id,
            'task_status' => $taskStatus,
            'user_id' => $userId,
            'input_type' => $inputType,
            'input' => $input,
            'result_type' => $resultType,
            'result' => $result,
        ]);
    }
}
