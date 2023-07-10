<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\MachineLearningTask;
use App\Models\TaskType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class EngineService
{
    private $client;

    private $baseUrl;

    private $token;

    private $baseHook;

    public function __construct()
    {
        $this->token = config('app.engine_api_token');
        $this->client = new Client(['headers' => ['X-API-Key' => $this->token]]);
        $this->baseUrl = config('app.engine_api_url');
        $this->baseHook = config('app.engine_hook_url');
    }

    private function createTask(string $taskId, int $taskTypeId, string $inputType, string $input, string $resultType)
    {
        MachineLearningTask::create([
            'task_id' => $taskId,
            'task_type_id' => $taskTypeId,
            'task_status' => 'PENDING',
            'user_id' => auth()->user()->id,
            'input_type' => $inputType,
            'input' => $input,
            'result_type' => $resultType,
            'result' => '',
        ]);
    }

    private function updateTask(string $taskId, string $taskStatus, string $result)
    {
        $task = MachineLearningTask::where('task_id', $taskId)->first();
        $task->task_status = $taskStatus;
        $task->result = $result;
        $task->save();
    }

    public function createTranslationTask(string $originalLanguage, string $targetLanguage, string $textType, string $text)
    {
        $url = '/translate';

        try {
            $result = $this->client->post($this->baseUrl.$url, [
                RequestOptions::JSON => [
                    'user' => strval(auth()->user()->id),
                    'original_language' => $originalLanguage,
                    'target_language' => $targetLanguage,
                    'text_type' => $textType,
                    'text' => $text,
                    'hook' => $this->baseHook.'/api/webhook/engine/translate',
                ],
            ]);
            $contents = json_decode($result->getBody()->getContents());

            $input = json_encode([
                'original_language' => $originalLanguage,
                'target_language' => $targetLanguage,
                'text_type' => $textType,
                'text' => $text,
            ]);

            $taskType = TaskType::where('name', 'TRANSLATION')->first();

            if (! $taskType) {
                throw new AppException('Error in Engine Service', 'Task Type not found');
            }

            $this->createTask($contents->task_id, $taskType->id, 'JSON', $input, 'TEXT');

            return $contents;
        } catch (GuzzleException $e) {
            throw new AppException('Error in Engine Service', $e->getMessage());
        }
    }

    public function getTranslationResult(string $taskId)
    {
        $task = MachineLearningTask::where('task_id', $taskId)->first();

        return ['status' => $task->task_status, 'text' => $task->result];
    }

    public function webhookTranslate(string $taskId, string $status, string $text)
    {
        $this->updateTask($taskId, $status, $text);
    }
}
