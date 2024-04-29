<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class TranslatePathTest extends TestCase
{
    #[DataProvider('providerTranslatePath')]
    public function testTranslatePath(string $path, ?string $locale, string $expected): void
    {
        $this->pageData->setPath($path);
        $actual = translate_path($this->pageData, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerTranslatePath(): array
    {
        return [
            ['/es/blog', 'ar', '/ar/blog'],
            ['/ar/blog', 'fr-CA', '/fr-CA/blog'],
            ['/fr-CA/blog', 'fr-CA', '/fr-CA/blog'],
            ['/ar/blog', 'haw-US', '/haw-US/blog'],
            ['/haw-US/blog', 'haw-US', '/haw-US/blog'],
            ['/fr-CA/blog', 'haw-US', '/haw-US/blog'],
            ['/haw-US/blog', 'fr-CA', '/fr-CA/blog'],
            ['/blog', 'ar', '/ar/blog'],
            ['/blog', 'en-UK', '/en-UK/blog'],
            ['/blog', 'haw-US', '/haw-US/blog'],
            ['/blog', packageDefaultLocale(), '/blog'],
            ['/ar/blog', null, '/blog'],
            ['/en-UK/blog', null, '/blog'],
            ['/haw-US/blog', null, '/blog'],
            ['/blog', null, '/blog'],
        ];
    }
}
