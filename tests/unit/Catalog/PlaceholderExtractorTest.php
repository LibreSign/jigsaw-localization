<?php

namespace Tests\Unit\Catalog;

use LibreSign\JigsawLocalization\Catalog\PlaceholderExtractor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PlaceholderExtractorTest extends TestCase
{
    #[DataProvider('placeholderCases')]
    public function test_it_extracts_stable_placeholder_tokens(string $text, array $expected): void
    {
        self::assertSame($expected, (new PlaceholderExtractor)->extract($text));
    }

    public static function placeholderCases(): array
    {
        return [
            'none' => ['Hello', []],
            'string' => ['By %s', ['1:s']],
            'integer' => ['Count: %d', ['1:d']],
            'width' => ['Item %02d', ['1:d']],
            'precision' => ['Total %.2f', ['1:f']],
            'multiple implicit' => ['%s has %d files', ['1:s', '2:d']],
            'positional reorder' => ['%2$d files by %1$s', ['1:s', '2:d']],
            'escaped percent' => ['100%% complete for %s', ['1:s']],
            'literal percent only' => ['100%% complete', []],
        ];
    }
}
