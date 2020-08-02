<?php

namespace perf\Translation;

use perf\Source\SourceInterface;
use perf\Translation\Exception\TranslationSourceException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class XmlTranslationSourceTest extends TestCase
{
    /**
     * @var SourceInterface|MockObject
     */
    private $source;

    private XmlTranslationSource $translationSource;

    protected function setUp(): void
    {
        if (!class_exists(SimpleXMLElement::class)) {
            $this->markTestSkipped('XML PHP extension not installed.');
        }

        $this->source = $this->createMock(SourceInterface::class);

        $this->translationSource = new XmlTranslationSource($this->source);
    }

    public function testTryGetTranslationWithEmptyXml()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenXml('');

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage(
            'Failed loading translations from XML source. << String could not be parsed as XML'
        );

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithInvalidXml()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenXml('bad_xml');

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage(
            'Failed loading translations from XML source. << String could not be parsed as XML'
        );

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithEmptyRootNode()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenXml('<test/>');

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage('No translation found in XML source.');

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithMissingLanguage()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenXml('<test><translation key="bar" />baz</test>');

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage('Translation file not valid: translation has no language Id.');

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithMissingKey()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenXml('<test><translation lang="foo" />baz</test>');

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage('Translation file not valid: translation has no key.');

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithOneMatchingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';
        $string     = 'baz';

        $this->givenXml(
            '<test><translation lang="' . $languageId . '" key="' . $key . '">' . $string . '</translation></test>'
        );

        $result = $this->translationSource->tryGetTranslation($languageId, $key);

        $this->assertInstanceOf(Translation::class, $result);
        $this->assertSame($string, $result->render());
    }

    public function testTryGetTranslationWithNoMatchingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';
        $string     = 'baz';

        $this->givenXml(
            '<test><translation lang="' . $languageId . '" key="' . $key . '">' . $string . '</translation></test>'
        );

        $result = $this->translationSource->tryGetTranslation('other-language-id', 'other-key');

        $this->assertNull($result);
    }

    private function givenXml($xml): void
    {
        $this->source->expects($this->once())->method('getContent')->willReturn($xml);
    }
}
