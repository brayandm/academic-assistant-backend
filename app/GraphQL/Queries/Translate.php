<?php

namespace App\GraphQL\Queries;

use App\Services\EngineService;

final class Translate
{
    private EngineService $engineService;

    public function __construct(EngineService $engineService)
    {
        $this->engineService = $engineService;
    }
}
