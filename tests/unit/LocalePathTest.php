<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class LocalePathTest extends TestCase
{
    #[DataProvider('providerLocalePath')]
    public function testLocalePath(string $path, ?string $locale, string $expected)
    {
        $actual = locale_path($this->pageData, $path, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function providerLocalePath(): array
    {
        return [
            ['blog', null, '/blog'],
            ['blog', 'ar', '/ar/blog'],
        ];
    }
}
