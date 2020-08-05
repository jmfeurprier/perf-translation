<?php

namespace perf\Translation;

use perf\Source\Exception\SourceException;
use perf\Source\SourceInterface;
use perf\Translation\Exception\TranslationSourceException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser as YamlParser;

class YamlTranslationSourceTest extends TestCase
{
    /**
     * @var YamlParser|MockObject
     */
    private $yamlParser;

    /**
     * @var SourceInterface|MockObject
     */
    private $source;

    private YamlTranslationSource $translationSource;

    protected function setUp(): void
    {
        if (!class_exists(YamlParser::class)) {
            $this->markTestSkipped('symfony/yaml not installed.');
        }

        $this->yamlParser = $this->createMock(YamlParser::class);

        $this->source = $this->createMock(SourceInterface::class);

        $this->translationSource = new YamlTranslationSource($this->yamlParser, $this->source);
    }

    public function testTryGetTranslationWithSourceException()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $sourceException = $this->createMock(SourceException::class);

        $this->yamlParser->expects($this->once())->method('parse')->willThrowException($sourceException);

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage(
            "Failed to import YAML translation file << Could not read from YAML source file."
        );

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithParserException()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $parseException = $this->createMock(ParseException::class);

        $this->yamlParser->expects($this->once())->method('parse')->willThrowException($parseException);

        $this->expectException(TranslationSourceException::class);
        $this->expectExceptionMessage(
            'Failed to import YAML translation file << Could not parse YAML source file.'
        );

        $this->translationSource->tryGetTranslation($languageId, $key);
    }

    public function testTryGetTranslationWithEmptyYaml()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenParsedYaml([]);

        $result = $this->translationSource->tryGetTranslation($languageId, $key);

        $this->assertNull($result);
    }

    public function testTryGetTranslationWithEmptyLanguageNode()
    {
        $languageId = 'foo';
        $key        = 'bar';

        $this->givenParsedYaml(
            [
                $languageId => [],
            ]
        );

        $result = $this->translationSource->tryGetTranslation($languageId, $key);

        $this->assertNull($result);
    }

    public function testTryGetTranslationWithOneMatchingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';
        $message    = 'baz';

        $this->givenParsedYaml(
            [
                $languageId => [
                    $key => $message,
                ],
            ]
        );

        $result = $this->translationSource->tryGetTranslation($languageId, $key);

        $this->assertSame($message, $result->render());
    }

    public function testTryGetTranslationWithNoMatchingTranslation()
    {
        $languageId = 'foo';
        $key        = 'bar';
        $message    = 'baz';

        $this->givenParsedYaml(
            [
                $languageId => [
                    $key => $message,
                ],
            ]
        );

        $result = $this->translationSource->tryGetTranslation('other-language-id', 'other-key');

        $this->assertNull($result);
    }

    public function testGetTranslationsWithDeepNamespacing()
    {
        $this->givenParsedYaml(
            [
                'en' => [
                    'foo' => [
                        'bar' => [
                            'baz' => 'qux',
                            'abc' => 'def',
                        ],
                        'ghi' => 'jkl',
                    ],
                    'mno' => 'pqr',
                ],
            ]
        );

        $result = $this->translationSource->getTranslations();

        $expected = [
            'en' => [
                'foo.bar.baz' => 'qux',
                'foo.bar.abc' => 'def',
                'foo.ghi'     => 'jkl',
                'mno'         => 'pqr',
            ],
        ];

        foreach ($expected as $languageId => $languageTranslations) {
            foreach ($languageTranslations as $key => $message) {
                $this->assertSame($message, $result->tryGet($languageId, $key)->render());
            }
        }
    }

    private function givenParsedYaml($parsed): void
    {
        $this->source->expects($this->once())->method('getContent')->willReturn('');

        $this->yamlParser->expects($this->once())->method('parse')->willReturn($parsed);
    }
}
