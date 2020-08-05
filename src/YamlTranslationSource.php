<?php

namespace perf\Translation;

use perf\Source\Exception\SourceException;
use perf\Source\SourceInterface;
use perf\Translation\Exception\TranslationSourceException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;

class YamlTranslationSource implements TranslationSourceInterface
{
    private const NAMESPACE_SEPARATOR = '.';

    private YamlParser $yamlParser;

    private SourceInterface $yamlSource;

    private array $translations = [];

    private ?TranslationCollection $translationCollection;

    public function __construct(YamlParser $yamlParser, SourceInterface $yamlSource)
    {
        $this->yamlParser = $yamlParser;
        $this->yamlSource = $yamlSource;
    }

    /**
     * {@inheritDoc}
     */
    public function tryGetTranslation(string $languageId, string $key): ?TranslationInterface
    {
        return $this->getTranslations()->tryGet($languageId, $key);
    }

    /**
     * {@inheritDoc}
     */
    public function getTranslations(): TranslationCollection
    {
        if (!isset($this->translationCollection)) {
            $this->translationCollection = $this->importTranslations();
        }

        return $this->translationCollection;
    }

    /**
     * @return TranslationCollection
     *
     * @throws TranslationSourceException
     */
    private function importTranslations(): TranslationCollection
    {
        $this->translations = [];

        try {
            $data = $this->yamlParser->parse($this->yamlSource->getContent());
        } catch (SourceException $e) {
            throw new TranslationSourceException(
                "Failed to import YAML translation file << Could not read from YAML source file."
            );
        } catch (ParseException $e) {
            throw new TranslationSourceException(
                "Failed to import YAML translation file << Could not parse YAML source file."
            );
        }

        $this->parseTranslations($data);

        return new TranslationCollection($this->translations);
    }

    private function parseTranslations(array $data): void
    {
        foreach ($data as $languageId => $languageTranslations) {
            $this->parseLanguageTranslations($languageId, $languageTranslations);
        }
    }

    private function parseLanguageTranslations(
        string $languageId,
        array $languageTranslations,
        string $keyPrefix = null
    ): void {
        foreach ($languageTranslations as $key => $message) {
            if (null !== $keyPrefix) {
                $key = $keyPrefix . self::NAMESPACE_SEPARATOR . $key;
            }

            if (is_array($message)) {
                $this->parseLanguageTranslations($languageId, $message, $key);

                continue;
            }

            $this->addTranslation($languageId, $key, $message);
        }
    }

    private function addTranslation(string $languageId, string $key, string $message): void
    {
        $this->translations[] = new Translation($languageId, $key, $message);
    }
}
