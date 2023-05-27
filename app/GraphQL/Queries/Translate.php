<?php

namespace App\GraphQL\Queries;

use App\Services\TranslationService;

final class Translate
{
    private TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function translate($root, array $args)
    {
        $originalLanguage = $args['original_language'];
        $targetLanguage = $args['target_language'];
        $textType = $args['text_type'];
        $text = $args['text'];

        return $this->translationService->translate($originalLanguage, $targetLanguage, $textType, $text);
    }
}
