<?php

namespace Tests\Unit\Locale;

use LibreSign\JigsawLocalization\Locale\AvailableLocaleMap;
use PHPUnit\Framework\TestCase;

final class AvailableLocaleMapTest extends TestCase
{
    public function test_default_locale_uses_an_empty_url_key(): void
    {
        $map = (new AvailableLocaleMap)->build(
            ['en', 'pt-BR', 'fr'],
            'en',
            [
                'en' => 'English',
                'pt-BR' => 'Português',
            ],
        );

        self::assertSame([
            '' => 'English',
            'pt-BR' => 'Português',
            'fr' => 'fr',
        ], $map);
    }
}
