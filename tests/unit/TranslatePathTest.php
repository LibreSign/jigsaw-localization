<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class TranslatePathTest extends TestCase
{
    #[DataProvider('providerTranslatePath')]
    public function test_translate_path(string $path, ?string $locale, string $expected): void
    {
        $this->pageData->setPath($path);
        $actual = translate_path($this->pageData, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerTranslatePath(): array
    {
        return [
            // Translating from one locale to another
            ['/es/blog', 'ar', '/ar/blog'],
            ['/ar/blog', 'fr-CA', '/fr-CA/blog'],
            ['/ar/blog', 'haw-US', '/haw-US/blog'],
            ['/fr-CA/blog', 'haw-US', '/haw-US/blog'],
            ['/haw-US/blog', 'fr-CA', '/fr-CA/blog'],

            // Keeping the same locale (should remain unchanged)
            ['/fr-CA/blog', 'fr-CA', '/fr-CA/blog'],
            ['/haw-US/blog', 'haw-US', '/haw-US/blog'],
            ['/ar/blog', 'ar', '/ar/blog'],
            ['/en-UK/blog', 'en-UK', '/en-UK/blog'],

            // Translating a path without a locale to a new locale
            ['/blog', 'ar', '/ar/blog'],
            ['/blog', 'en-UK', '/en-UK/blog'],
            ['/blog', 'haw-US', '/haw-US/blog'],

            // Converting to the default locale (removing locale prefix)
            ['/ar/blog', packageDefaultLocale(), '/blog'],
            ['/fr-CA/blog', packageDefaultLocale(), '/blog'],
            ['/haw-US/blog', packageDefaultLocale(), '/blog'],

            // If `target_locale` is `null`, it should return to the default locale
            ['/ar/blog', null, '/blog'],
            ['/en-UK/blog', null, '/blog'],
            ['/haw-US/blog', null, '/blog'],
            ['/blog', null, '/blog'],

            // If already in the default locale and `target_locale` is `null`
            ['/about', null, '/about'],
            ['/contact', null, '/contact'],

            // If `target_locale` is the same as `current_locale`
            ['/es', 'es', '/es'],
            ['/es/contact', 'es', '/es/contact'],
            ['/fr-CA/page', 'fr-CA', '/fr-CA/page'],

            // Testing multi-segment paths
            ['/es/blog/posts/123', 'ar', '/ar/blog/posts/123'],
            ['/ar/news/article/456', 'fr-CA', '/fr-CA/news/article/456'],
            ['/haw-US/shop/products/789', 'en-UK', '/en-UK/shop/products/789'],

            // Testing `/` as a root path (edge case)
            ['/', 'ar', '/ar'],
            ['/', null, ''],
            ['/', packageDefaultLocale(), ''],

            // Testing paths with unrecognized prefixes (should keep them)
            ['/custom-path/blog', 'fr-CA', '/fr-CA/custom-path/blog'],
            ['/random/path', 'es', '/es/random/path'],

            // Short prefixes that could cause substring errors
            ['/a/blog', 'ar', '/ar/a/blog'],
            ['/fr/page', 'haw-US', '/haw-US/page'],

            // Handling an empty path (should default to `/`)
            ['', 'ar', '/ar'],
            ['', null, ''],

            // Deeply nested paths
            ['/es/a/b/c/d/e', 'ar', '/ar/a/b/c/d/e'],
            ['/fr-CA/deeply/nested/path/example', 'en-UK', '/en-UK/deeply/nested/path/example'],

            // Paths with special characters
            ['/fr-CA/à-propos', 'es', '/es/à-propos'],
            ['/es/blog/café', 'ar', '/ar/blog/café'],
            ['/haw-US/ümlaut', 'en-UK', '/en-UK/ümlaut'],

            // Case sensitivity (should not modify case)
            ['/FR/blog', 'es', '/es/FR/blog'],
            ['/Es/blog', 'fr-CA', '/fr-CA/Es/blog'],

            // Handling trailing slashes
            ['/es/', 'ar', '/ar'],
            ['/fr-CA/', null, ''],
            ['/blog/', 'ar', '/ar/blog'],

        /**
         * @todo Unexpected locale prefixes (should behave as a normal path)
         */
        ];
    }
}
