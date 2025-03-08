<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class CurrentPathLocaleTest extends TestCase
{
    #[DataProvider('providerLanguageCode')]
    public function test_language_code($path, $expected): void
    {
        $this->pageData->setPath($path);
        $actual = current_path_locale($this->pageData);
        $this->assertEquals($expected, $actual);
    }

    public static function providerLanguageCode(): array
    {
        return [
            ['/', packageDefaultLocale()],
            ['/blog', packageDefaultLocale()],
            ['/es', 'es'],
            ['/es/blog', 'es'],
            ['/raw-US', 'raw-US'],
            ['/raw-US/blog', 'raw-US'],
        ];
    }
}
