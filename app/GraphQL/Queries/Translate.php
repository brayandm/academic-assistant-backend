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

    public function translate($root, array $args)
    {
        $originalLanguage = $args['original_language'];
        $targetLanguage = $args['target_language'];
        $textType = $args['text_type'];
        $text = $args['text'];

        return $this->engineService->translate($originalLanguage, $targetLanguage, $textType, $text);
    }
}
