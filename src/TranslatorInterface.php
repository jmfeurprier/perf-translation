<?php

namespace perf\Translation;

use perf\Translation\Exception\TranslationNotFoundException;

interface TranslatorInterface
{
    /**
     * @param string      $key
     * @param array       $values     Optional values to insert in the string (see "printf()" syntax).
     * @param null|string $languageId Optional language identifier.
     *
     * @return string
     *
     * @throws TranslationNotFoundException
     */
    public function translate(string $key, array $values = [], ?string $languageId = null): string;
}
