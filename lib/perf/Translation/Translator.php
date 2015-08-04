<?php

namespace perf\Translation;

/**
 *
 *
 */
class Translator
{

    /**
     *
     *
     * @var string
     */
    private $namespacesPath = '';

    /**
     *
     *
     * @var string
     */
    private $language = 'en';

    /**
     *
     *
     * @var {string:{string:string}}
     */
    private $strings = array();

    /**
     * Sets base path where namespace files can be found.
     *
     * @param string $namespacesPath
     * @return Translator Fluent return.
     */
    public function setNamespacesPath($path)
    {
        $this->namespacesPath = (string) $path;

        return $this;
    }

    /**
     * Sets the language to be used (typically a 2-byte string, like "en").
     *
     * @param string $language
     * @return Translator Fluent return.
     */
    public function setLanguage($language)
    {
        $this->language = (string) $language;

        return $this;
    }

    /**
     *
     *
     * @param string $namespace
     * @param string $stringKey
     * @param array $values Optional values to insert in the string (see "printf()" syntax).
     * @return string
     * @throws \DomainException
     */
    public function translate($namespace, $stringKey, array $values = array())
    {
        $namespace = (string) $namespace;

        // Namespace not loaded yet?
        if (!isset($this->strings[$namespace][$this->language])) {
            $this->loadNamespace($namespace);
        }

        if (isset($this->strings[$namespace][$this->language][$stringKey])) {
            return vsprintf($this->strings[$namespace][$this->language][$stringKey], $values);
        }

        throw new \DomainException('String not found.');
    }

    /**
     *
     *
     * @param string $namespace
     * @return void
     * @throws \RuntimeException
     */
    private function loadNamespace($namespace)
    {
        $namespace = (string) $namespace;

        $namespacePath     = $this->namespacesPath . $this->language . DIRECTORY_SEPARATOR;
        $namespaceFilename = $namespace . '.xml';

        try {
            $i18n = new \SimpleXMLElement($namespacePath . $namespaceFilename, 0, true);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to load i18n file.", 0, $e);
        }

        if (is_null($i18n->attributes()->namespace) || ($i18n->attributes()->namespace != $namespace)) {
            throw new \RuntimeException('I18n file not valid: namespace does not match.');
        }

        if (is_null($i18n->attributes()->language) || ($i18n->attributes()->language != $this->language)) {
            throw new \RuntimeException('I18n file not valid: language does not match.');
        }

        $strings = array();

        foreach ($i18n->string as $string) {
            $stringKey = $string->attributes()->key;

            if (is_null($stringKey)) {
                throw new \RuntimeException('Translation file not valid: string has no key.');
            }

            $strings[(string) $stringKey] = (string) $string;
        }

        $this->strings[$namespace][$this->language] = $strings;
    }
}
