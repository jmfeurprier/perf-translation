<?php

namespace perf\Translation;

use perf\Translation\Exception\TranslationSourceException;

interface TranslationSourceInterface
{
    /**
     * @param string $languageId
     * @param string $key
     *
     * @return null|TranslationInterface
     *
     * @throws TranslationSourceException
     */
    public function tryGetTranslation(string $languageId, string $key): ?TranslationInterface;

    /**
     * @return TranslationCollection
     *
     * @throws TranslationSourceException
     */
    public function getTranslations(): TranslationCollection;
}
