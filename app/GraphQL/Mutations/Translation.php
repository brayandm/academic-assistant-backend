<?php

namespace App\GraphQL\Mutations;

use App\Services\EngineService;

final class Translation
{
    private EngineService $engineService;

    public function __construct(EngineService $engineService)
    {
        $this->engineService = $engineService;
    }

    public function createTranslationTask($root, array $args)
    {
        $originalLanguage = $args['original_language'];
        $targetLanguage = $args['target_language'];
        $textType = $args['text_type'];
        $text = $args['text'];

        return $this->engineService->createTranslationTask($originalLanguage, $targetLanguage, $textType, $text);
    }
}
