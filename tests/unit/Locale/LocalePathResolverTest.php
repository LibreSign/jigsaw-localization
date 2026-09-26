<?php

namespace Tests\Unit\Locale;

use ElaborateCode\JigsawLocalization\Locale\LocalePathResolver;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocalePathResolverTest extends TestCase
{
    #[DataProvider('currentLocaleCases')]
    public function test_it_resolves_only_configured_locale_prefixes(string $path, string $expected): void
    {
        $resolver = $this->resolver();

        self::assertSame($expected, $resolver->currentLocale($path));
    }

    public static function currentLocaleCases(): array
    {
        return [
            'root' => ['/', 'en'],
            'default path' => ['/blog', 'en'],
            'language locale' => ['/es/blog', 'es'],
            'regional locale' => ['/pt-BR/blog', 'pt-BR'],
            'configured non-regex locale' => ['/zh_Hant_TW/blog', 'zh_Hant_TW'],
            'unconfigured language-looking prefix' => ['/de/blog', 'en'],
            'case sensitive' => ['/ES/blog', 'en'],
        ];
    }

    #[DataProvider('translateCases')]
    public function test_it_translates_paths(string $path, ?string $targetLocale, string $expected): void
    {
        self::assertSame($expected, $this->resolver()->translate($path, $targetLocale));
    }

    public static function translateCases(): array
    {
        return [
            'locale to locale' => ['/es/blog', 'pt-BR', '/pt-BR/blog'],
            'locale to default' => ['/pt-BR/blog', 'en', '/blog'],
            'default to locale' => ['/blog', 'es', '/es/blog'],
            'root to locale' => ['/', 'es', '/es'],
            'root to default' => ['/', null, '/'],
            'custom configured locale' => ['/zh_Hant_TW/docs', 'es', '/es/docs'],
            'unknown prefix is content' => ['/de/blog', 'es', '/es/de/blog'],
        ];
    }

    #[DataProvider('localizeCases')]
    public function test_it_localizes_partial_paths(string $path, string $targetLocale, string $expected): void
    {
        self::assertSame($expected, $this->resolver()->localize($path, $targetLocale));
    }

    public static function localizeCases(): array
    {
        return [
            'relative path' => ['blog', 'es', '/es/blog'],
            'absolute path' => ['/blog', 'es', '/es/blog'],
            'already prefixed path' => ['/es/blog', 'es', '/es/blog'],
            'locale root' => ['/es', 'es', '/es'],
            'default locale' => ['/blog', 'en', '/blog'],
            'root' => ['/', 'es', '/es'],
            'trailing slash' => ['/blog/', 'es', '/es/blog/'],
        ];
    }

    public function test_default_locale_is_required(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new LocalePathResolver('', ['en']);
    }

    private function resolver(): LocalePathResolver
    {
        return new LocalePathResolver('en', ['en', 'es', 'pt-BR', 'zh_Hant_TW']);
    }
}
