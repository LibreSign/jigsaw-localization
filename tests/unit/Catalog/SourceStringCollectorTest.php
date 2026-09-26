<?php

namespace Tests\Unit\Catalog;

use ElaborateCode\JigsawLocalization\Catalog\SourceStringCollector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class SourceStringCollectorTest extends TestCase
{
    public function test_it_collects_only_the_configured_source_locale(): void
    {
        $collector = new SourceStringCollector('en');

        $collector->collect('pt-BR', 'Olá');
        $collector->collect('en', 'Hello');

        self::assertSame(['Hello' => 'Hello'], $collector->all());
    }

    public function test_it_deduplicates_and_sorts_source_strings(): void
    {
        $collector = new SourceStringCollector('en');

        $collector->collect('en', 'Zulu');
        $collector->collect('en', 'Alpha');
        $collector->collect('en', 'Zulu');

        self::assertSame([
            'Alpha' => 'Alpha',
            'Zulu' => 'Zulu',
        ], $collector->all());
    }

    public function test_it_can_be_cleared(): void
    {
        $collector = new SourceStringCollector('en');
        $collector->collect('en', 'Hello');

        $collector->clear();

        self::assertSame([], $collector->all());
    }

    public function test_source_locale_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SourceStringCollector('');
    }
}
