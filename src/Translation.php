<?php

namespace perf\Translation;

class Translation implements TranslationInterface
{
    private string $languageId;

    private string $key;

    private string $message;

    public function __construct(string $languageId, string $key, string $message)
    {
        $this->languageId = $languageId;
        $this->key        = $key;
        $this->message     = $message;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $values = []): string
    {
        return vsprintf($this->message, $values);
    }
}
