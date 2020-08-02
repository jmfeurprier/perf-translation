<?php

namespace perf\Translation;

use perf\Caching\CacheClient;
use RuntimeException;

class TranslatorBuilder
{
    /**
     * @var TranslationSourceInterface[]
     */
    private array $sources = [];

    private CacheClient $cacheClient;

    private ?string $defaultLanguageId;

    public function addSource(TranslationSourceInterface $source): self
    {
        $this->sources[] = $source;

        return $this;
    }

    public function setDefaultLanguageId(string $id): self
    {
        $this->defaultLanguageId = $id;

        return $this;
    }

    public function setCacheClient(CacheClient $client): self
    {
        $this->cacheClient = $client;

        return $this;
    }

    /**
     * @return Translator
     *
     * @throws RuntimeException
     */
    public function build(): Translator
    {
        $source = $this->getSource();

        return new Translator($source, $this->defaultLanguageId);
    }

    /**
     * @return TranslationSourceInterface
     *
     * @throws RuntimeException
     */
    private function getSource(): TranslationSourceInterface
    {
        if (count($this->sources) < 1) {
            throw new RuntimeException('No translation source provided.');
        }

        $translationSource = new CompositeTranslationSource($this->sources);

        if (isset($this->cacheClient)) {
            $translationSource = new CacheableTranslationSource($this->cacheClient, $translationSource);
        }

        return $translationSource;
    }
}
