<?php

namespace perf\Translation;

class CompositeTranslationSource implements TranslationSourceInterface
{
    /**
     * @var TranslationSourceInterface[]
     */
    private array $sources = [];

    /**
     * @param TranslationSourceInterface[] $sources
     */
    public function __construct(array $sources)
    {
        foreach ($sources as $source) {
            $this->addSource($source);
        }
    }

    private function addSource(TranslationSourceInterface $source): void
    {
        $this->sources[] = $source;
    }

    public function tryGetTranslation(string $languageId, string $key): ?TranslationInterface
    {
        foreach ($this->sources as $source) {
            $translation = $source->tryGetTranslation($languageId, $key);

            if ($translation) {
                return $translation;
            }
        }

        return null;
    }

    public function getTranslations(): TranslationCollection
    {
        $translations = [];

        foreach ($this->sources as $source) {
            foreach ($source->getTranslations() as $translation) {
                $translations[] = $translation;
            }
        }

        return new TranslationCollection($translations);
    }
}
