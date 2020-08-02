<?php

namespace perf\Translation\Exception;

class TranslationNotFoundException extends \Exception
{
    private string $languageId;

    private string $key;

    public function __construct(string $languageId, string $key)
    {
        $message = "Translation not found (language: {$languageId}, key: {$key}).";

        parent::__construct($message);

        $this->languageId = $languageId;
        $this->key        = $key;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
