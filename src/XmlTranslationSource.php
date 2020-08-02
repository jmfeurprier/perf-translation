<?php

namespace perf\Translation;

use Exception;
use perf\Source\SourceInterface;
use perf\Translation\Exception\TranslationSourceException;
use SimpleXMLElement;

class XmlTranslationSource implements TranslationSourceInterface
{
    private SourceInterface $xmlSource;

    private ?TranslationCollection $translations;

    public function __construct(SourceInterface $xmlSource)
    {
        $this->xmlSource = $xmlSource;
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
        if (!isset($this->translations)) {
            $this->translations = $this->importTranslations();
        }

        return $this->translations;
    }

    /**
     * @return TranslationCollection
     *
     * @throws TranslationSourceException
     */
    private function importTranslations(): TranslationCollection
    {
        try {
            $xml = $this->xmlSource->getContent();

            $sxe = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            throw new TranslationSourceException(
                "Failed loading translations from XML source. << {$e->getMessage()}",
                0,
                $e
            );
        }

        if (count($sxe->translation) < 1) {
            throw new TranslationSourceException('No translation found in XML source.');
        }

        $translations = [];

        foreach ($sxe->translation as $sxeTranslation) {
            $languageId = (string) $sxeTranslation['lang'];
            $key        = (string) $sxeTranslation['key'];

            if ('' === $languageId) {
                throw new TranslationSourceException(
                    'Translation file not valid: translation has no language Id.'
                );
            }

            if ('' === $key) {
                throw new TranslationSourceException(
                    'Translation file not valid: translation has no key.'
                );
            }

            $string = (string) $sxeTranslation;

            $translations[] = new Translation($languageId, $key, $string);
        }

        return new TranslationCollection($translations);
    }
}
