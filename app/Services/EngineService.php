<?php

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\EngineTask;
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

    private function createTask(string $taskId, string $taskType, string $resultType)
    {
        EngineTask::create([
            'task_id' => $taskId,
            'task_type' => $taskType,
            'task_status' => 'PENDING',
            'user_id' => auth()->user()->id,
            'result_type' => $resultType,
            'result' => '',
        ]);
    }

    private function updateTask(string $taskId, string $taskStatus, string $result)
    {
        $task = EngineTask::where('task_id', $taskId)->first();
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
                    'user' => auth()->user()->id,
                    'original_language' => $originalLanguage,
                    'target_language' => $targetLanguage,
                    'text_type' => $textType,
                    'text' => $text,
                    'hook' => $this->baseHook.'/api/webhook/engine/translate',
                ],
            ]);
            $contents = json_decode($result->getBody()->getContents());

            $this->createTask($contents->task_id, 'TRANSLATION', 'TEXT');

            return $contents;
        } catch (GuzzleException $e) {
            throw new AppException('Error in Engine Service', $e->getMessage());
        }
    }

    public function getTranslationResult(string $taskId)
    {
        $task = EngineTask::where('task_id', $taskId)->first();

        return ['status' => $task->task_status, 'text' => $task->result];
    }

    public function webhookTranslate(string $taskId, string $status, string $text)
    {
        $this->updateTask($taskId, $status, $text);
    }
}
