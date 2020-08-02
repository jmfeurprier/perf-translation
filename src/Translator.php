<?php

namespace perf\Translation;

use perf\Translation\Exception\TranslationNotFoundException;

class Translator implements TranslatorInterface
{
    private TranslationSourceInterface $source;

    private ?string $defaultLanguageId;

    public static function createBuilder(): TranslatorBuilder
    {
        return new TranslatorBuilder();
    }

    public function __construct(TranslationSourceInterface $source, ?string $defaultLanguageId = null)
    {
        $this->source            = $source;
        $this->defaultLanguageId = $defaultLanguageId;
    }

    /**
     * {@inheritDoc}
     */
    public function translate(string $key, array $values = [], ?string $languageId = null): string
    {
        if (null === $languageId) {
            $languageId = $this->defaultLanguageId;
        }

        $translation = $this->source->tryGetTranslation($languageId, $key);

        if ($translation) {
            return $translation->render($values);
        }

        throw new TranslationNotFoundException($languageId, $key);
    }
}
