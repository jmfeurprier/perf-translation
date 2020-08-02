<?php

namespace perf\Translation;

class TranslationCollection
{
    /**
     * @var {string:{string:TranslationInterface}}
     */
    private array $translations = [];

    /**
     * @param TranslationInterface[] $translations
     */
    public function __construct(array $translations)
    {
        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }
    }

    private function addTranslation(TranslationInterface $translation): void
    {
        $key        = $translation->getKey();
        $languageId = $translation->getLanguageId();

        $this->translations[$languageId][$key] = $translation;
    }

    public function tryGet(string $languageId, string $key): ?TranslationInterface
    {
        return $this->translations[$languageId][$key] ?? null;
    }
}
