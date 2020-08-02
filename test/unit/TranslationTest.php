<?php

namespace perf\Translation;

use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testGetLanguage()
    {
        $language = 'foo';
        $key      = 'bar';
        $message  = 'qux';

        $translation = new Translation($language, $key, $message);

        $this->assertSame($language, $translation->getLanguageId());
    }

    public function testGetKey()
    {
        $language = 'foo';
        $key      = 'bar';
        $message  = 'qux';

        $translation = new Translation($language, $key, $message);

        $this->assertSame($key, $translation->getKey());
    }

    public function testRenderWithoutValues()
    {
        $language = 'foo';
        $key      = 'bar';
        $message  = 'qux';

        $translation = new Translation($language, $key, $message);

        $this->assertSame($message, $translation->render());
    }

    public function testRenderWithValues()
    {
        $language = 'foo';
        $key      = 'bar';
        $message  = 'qux %s';
        $values   = [
            'abc',
        ];

        $translation = new Translation($language, $key, $message);

        $this->assertSame('qux abc', $translation->render($values));
    }
}
