<?php

namespace perf\Translation;

use perf\Translation\Exception\TranslationNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /**
     * @var TranslationSourceInterface|MockObject
     */
    private $source;

    protected function setUp(): void
    {
        $this->source = $this->createMock(TranslationSourceInterface::class);
    }

    public function testTranslateWithMissingTranslation()
    {
        $key               = 'foo';
        $values            = [];
        $languageId        = 'bar';
        $defaultLanguageId = null;

        $this->source->expects($this->once())->method('tryGetTranslation')->with($languageId, $key)->willReturn(null);

        $translator = new Translator($this->source, $defaultLanguageId);

        $this->expectException(TranslationNotFoundException::class);

        $translator->translate($key, $values, $languageId);
    }

    public function testTranslateWithExistingTranslation()
    {
        $key               = 'foo';
        $values            = [];
        $languageId        = 'bar';
        $defaultLanguageId = null;
        $translatedContent = 'baz';

        $translation = $this->createTranslation($values, $translatedContent);

        $this->source
            ->expects($this->once())
            ->method('tryGetTranslation')
            ->with($languageId, $key)
            ->willReturn($translation);

        $translator = new Translator($this->source, $defaultLanguageId);

        $result = $translator->translate($key, $values, $languageId);

        $this->assertIsString($result);
        $this->assertSame($translatedContent, $result);
    }

    public function testTranslateWithExistingTranslationAndValues()
    {
        $key               = 'foo';
        $values            = [
            'abc',
            'def',
        ];
        $languageId        = 'bar';
        $defaultLanguageId = null;
        $translatedContent = 'baz';

        $translation = $this->createTranslation($values, $translatedContent);

        $this->source
            ->expects($this->once())
            ->method('tryGetTranslation')
            ->with($languageId, $key)
            ->willReturn($translation);

        $translator = new Translator($this->source, $defaultLanguageId);

        $result = $translator->translate($key, $values, $languageId);

        $this->assertIsString($result);
        $this->assertSame($translatedContent, $result);
    }

    private function createTranslation($expectedValues, $translatedContent): Translation
    {
        $translation = $this->createMock(Translation::class);

        $translation
            ->expects($this->atLeastOnce())
            ->method('render')
            ->with($expectedValues)
            ->willReturn($translatedContent);

        return $translation;
    }
}
