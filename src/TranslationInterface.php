<?php

namespace perf\Translation;

interface TranslationInterface
{
    public function getLanguageId(): string;

    public function getKey(): string;

    /**
     * @param string[] $values Optional values to insert in the string (see "printf()" syntax).
     *
     * @return string
     */
    public function render(array $values = []): string;
}
