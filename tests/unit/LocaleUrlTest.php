<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class LocaleUrlTest extends TestCase
{
    #[DataProvider('providerLocaleUrl')]
    public function testLocaleUrl(string $path, ?string $locale, string $expected): void
    {
        $this->app->config = collect(['baseUrl' => 'https://elaboratecode.com/packages']);
        $actual = locale_url($this->pageData, $path, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerLocaleUrl(): array
    {
        return [
            ['blog', null, 'https://elaboratecode.com/packages/blog'],
            ['blog', 'ar', 'https://elaboratecode.com/packages/ar/blog'],
        ];
    }
}
