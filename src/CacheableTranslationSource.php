<?php

namespace perf\Translation;

use perf\Caching\CacheClient;

class CacheableTranslationSource implements TranslationSourceInterface
{
    private CacheClient $cacheClient;

    private TranslationSourceInterface $source;

    public function __construct(CacheClient $cacheClient, TranslationSourceInterface $source)
    {
        $this->cacheClient = $cacheClient;
        $this->source      = $source;
    }

    public function tryGetTranslation(string $languageId, string $key): ?Translation
    {
        $cacheKey = $this->getCacheKey($languageId, $key);

        $translation = $this->cacheClient->tryFetch($cacheKey);

        if (!$translation) {
            $translation = $this->source->tryGetTranslation($languageId, $key);

            $this->cacheClient->store($cacheKey, $translation);
        }

        return $translation;
    }

    private function getCacheKey(string $languageId, string $key): string
    {
        return "TRANSLATION.{$languageId}.{$key}";
    }

    public function getTranslations(): TranslationCollection
    {
        return $this->source->getTranslations();
    }
}
