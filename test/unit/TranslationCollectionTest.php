<?php

namespace perf\Translation;

use PHPUnit\Framework\TestCase;

class TranslationCollectionTest extends TestCase
{
    public function testTryGetWithNotExistingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $translation = $this->createTranslation($languageId, $key);

        $collection = new TranslationCollection(
            [
                $translation,
            ]
        );

        $this->assertNull($collection->tryGet('not-existing', 'not-existing'));
    }

    public function testTryGetWithExistingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $translation = $this->createTranslation($languageId, $key);

        $collection = new TranslationCollection(
            [
                $translation,
            ]
        );

        $this->assertSame($translation, $collection->tryGet($languageId, $key));
    }

    public function testTryGetWithNotExistingLanguageId()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $translation = $this->createTranslation($languageId, $key);

        $collection = new TranslationCollection(
            [
                $translation,
            ]
        );

        $this->assertNull($collection->tryGet('not-existing', $key));
    }

    public function testTryGetWithNotExistingKey()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $translation = $this->createTranslation($languageId, $key);

        $collection = new TranslationCollection(
            [
                $translation,
            ]
        );

        $this->assertNull($collection->tryGet($languageId, 'not-existing'));
    }

    private function createTranslation(string $languageId, string $key)
    {
        $translation = $this->createMock(Translation::class);
        $translation->expects($this->atLeastOnce())->method('getLanguageId')->willReturn($languageId);
        $translation->expects($this->atLeastOnce())->method('getKey')->willReturn($key);

        return $translation;
    }
}
