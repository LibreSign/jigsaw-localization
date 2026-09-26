<?php

namespace Tests\Unit\Extraction;

use LibreSign\JigsawLocalization\Extraction\ExtractedString;
use LibreSign\JigsawLocalization\Extraction\ExtractedStringNormalizer;
use LibreSign\JigsawLocalization\Extraction\TranslationSource;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use UnexpectedValueException;

final class ExtractedStringNormalizerTest extends TestCase
{
    public function test_string_results_inherit_the_translation_source_identifier(): void
    {
        $source = new TranslationSource('en', 'home.blade.php', 'ignored');

        $result = (new ExtractedStringNormalizer)->normalize('Hello', $source, 'ExampleExtractor');

        self::assertSame('Hello', $result->text());
        self::assertSame('home.blade.php', $result->source());
    }

    public function test_extracted_string_instances_are_preserved(): void
    {
        $source = new TranslationSource('en', 'home.blade.php', 'ignored');
        $expected = new ExtractedString('Hello', 'custom-source', 12, 'title');

        $actual = (new ExtractedStringNormalizer)->normalize($expected, $source, 'ExampleExtractor');

        self::assertSame($expected, $actual);
    }

    #[DataProvider('invalidResults')]
    public function test_invalid_extractor_results_are_rejected(mixed $value): void
    {
        $source = new TranslationSource('en', 'home.blade.php', 'ignored');

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('ExampleExtractor');

        (new ExtractedStringNormalizer)->normalize($value, $source, 'ExampleExtractor');
    }

    public static function invalidResults(): array
    {
        return [
            'null' => [null],
            'integer' => [123],
            'array' => [[]],
            'object' => [new stdClass],
        ];
    }
}
