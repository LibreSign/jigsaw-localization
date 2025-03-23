<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class LocalePathTest extends TestCase
{
    #[DataProvider('providerLocalePath')]
    public function test_locale_path(string $path, ?string $locale, string $expected)
    {
        $actual = locale_path($this->pageData, $path, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerLocalePath(): array
    {
        return [
            // Handling empty paths
            ['', null, '/'],
            ['', 'ar', '/ar'],

            // Root path handling
            ['/', null, '/'],
            ['/', 'ar', '/ar'],
            ['/', packageDefaultLocale(), '/'],

            // Basic path translation
            ['blog', null, '/blog'],
            ['blog', 'ar', '/ar/blog'],
            ['blog/', 'ar', '/ar/blog/'],
            ['/blog/', 'ar', '/ar/blog/'],

            // Ensuring paths already prefixed with locale remain unchanged
            ['/ar/blog', 'ar', '/ar/blog'],
            ['/fr-CA/blog', 'fr-CA', '/fr-CA/blog'],

            // Paths when `packageDefaultLocale()` should not add a locale prefix
            ['blog', packageDefaultLocale(), '/blog'],
            ['/blog', packageDefaultLocale(), '/blog'],

            // Special characters handling
            ['café', 'fr', '/fr/café'],
            ['ação', 'pt-BR', '/pt-BR/ação'],
            ['ümlaut', 'de', '/de/ümlaut'],

            // Case sensitivity (if applicable)
            ['Blog', 'es', '/es/Blog'],
            ['NEWS', 'es', '/es/NEWS'],

            // Multi-segment paths
            ['blog/posts/123', 'ar', '/ar/blog/posts/123'],
            ['es/news/article/456', 'fr-CA', '/fr-CA/es/news/article/456'],

            // Ensuring leading and trailing slashes are handled correctly
            ['/news', 'es', '/es/news'],
            ['news/', 'es', '/es/news/'],
            ['/news/', 'es', '/es/news/'],
        ];
    }
}
