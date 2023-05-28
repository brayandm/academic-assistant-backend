<?php

namespace App\GraphQL\Queries;

use App\Services\EngineService;

final class Translation
{
    private EngineService $engineService;

    public function __construct(EngineService $engineService)
    {
        $this->engineService = $engineService;
    }

    public function getTranslationResult($root, array $args)
    {
        $taskId = $args['task_id'];

        return $this->engineService->getTranslationResult($taskId);
    }
}
