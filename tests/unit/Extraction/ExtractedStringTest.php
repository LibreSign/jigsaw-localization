<?php

namespace Tests\Unit\Extraction;

use InvalidArgumentException;
use LibreSign\JigsawLocalization\Extraction\ExtractedString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ExtractedStringTest extends TestCase
{
    public function test_it_keeps_optional_source_information(): void
    {
        $string = new ExtractedString('Hello', 'home.blade.php', 12, 'hero');

        self::assertSame('Hello', $string->text());
        self::assertSame('home.blade.php', $string->source());
        self::assertSame(12, $string->line());
        self::assertSame('hero', $string->context());
    }

    #[DataProvider('emptyRequiredValues')]
    public function test_required_values_cannot_be_empty(string $text, string $source): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExtractedString($text, $source);
    }

    public static function emptyRequiredValues(): array
    {
        return [
            'text' => ['', 'home.blade.php'],
            'source' => ['Hello', ''],
        ];
    }

    #[DataProvider('invalidLines')]
    public function test_invalid_lines_are_rejected(int $line): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExtractedString('Hello', 'home.blade.php', $line);
    }

    public static function invalidLines(): array
    {
        return [
            'zero' => [0],
            'negative' => [-1],
        ];
    }
}
