<?php

namespace Tests\Unit\Extraction;

use ElaborateCode\JigsawLocalization\Extraction\ExtractedString;
use InvalidArgumentException;
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

    public function test_empty_text_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExtractedString('', 'home.blade.php');
    }

    public function test_invalid_line_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExtractedString('Hello', 'home.blade.php', 0);
    }
}
