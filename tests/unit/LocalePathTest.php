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
            ['', null, ''],
            ['', 'ar', '/ar'],
            ['blog', null, '/blog'],
            ['blog', 'ar', '/ar/blog'],
            ['blog/', 'ar', '/ar/blog/'],
            ['/blog/', 'ar', '/ar/blog/'],
        ];
    }
}
