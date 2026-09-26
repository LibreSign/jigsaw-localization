<?php

namespace Tests\Unit\Extraction;

use ElaborateCode\JigsawLocalization\Extraction\TranslationSource;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TranslationSourceTest extends TestCase
{
    public function test_it_exposes_locale_identifier_contents_and_metadata(): void
    {
        $source = new TranslationSource('en', 'posts/example.md', 'Hello', ['type' => 'markdown']);

        self::assertSame('en', $source->locale());
        self::assertSame('posts/example.md', $source->identifier());
        self::assertSame('Hello', $source->contents());
        self::assertSame(['type' => 'markdown'], $source->metadata());
    }

    public function test_locale_is_required(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TranslationSource('', 'page.md', 'Hello');
    }

    public function test_identifier_is_required(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TranslationSource('en', '', 'Hello');
    }
}
