<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class TranslateUrlTest extends TestCase
{
    #[DataProvider('providerTranslateUrl')]
    public function testTranslateUrl(string $path, ?string $locale, string $expected): void
    {
        $this->pageData->setPath($path);
        $actual = translate_url($this->pageData, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerTranslateUrl(): array
    {
        return [
            ['/es/blog', 'ar', 'https://elaboratecode.com/packages/ar/blog'],
            ['/ar/blog', 'fr-CA', 'https://elaboratecode.com/packages/fr-CA/blog'],
            ['/fr-CA/blog', 'fr-CA', 'https://elaboratecode.com/packages/fr-CA/blog'],
            ['/ar/blog', 'haw-US', 'https://elaboratecode.com/packages/haw-US/blog'],
            ['/haw-US/blog', 'haw-US', 'https://elaboratecode.com/packages/haw-US/blog'],
            ['/fr-CA/blog', 'haw-US', 'https://elaboratecode.com/packages/haw-US/blog'],
            ['/haw-US/blog', 'fr-CA', 'https://elaboratecode.com/packages/fr-CA/blog'],
            ['/blog', 'ar', 'https://elaboratecode.com/packages/ar/blog'],
            ['/blog', 'en-UK', 'https://elaboratecode.com/packages/en-UK/blog'],
            ['/blog', 'haw-US', 'https://elaboratecode.com/packages/haw-US/blog'],
            ['/blog', packageDefaultLocale(), 'https://elaboratecode.com/packages/blog'],
            ['/ar/blog', null, 'https://elaboratecode.com/packages/blog'],
            ['/en-UK/blog', null, 'https://elaboratecode.com/packages/blog'],
            ['/haw-US/blog', null, 'https://elaboratecode.com/packages/blog'],
            ['/blog', null, 'https://elaboratecode.com/packages/blog'],
        ];
    }
}
