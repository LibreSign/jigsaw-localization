<?php

namespace Tests\Unit\Catalog;

use ElaborateCode\JigsawLocalization\Catalog\TranslationCatalogLocator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslationCatalogLocatorTest extends TestCase
{
    public function test_it_builds_catalog_paths_without_restricting_locale_conventions(): void
    {
        $locator = new TranslationCatalogLocator('translations');

        self::assertSame(
            'translations'.DIRECTORY_SEPARATOR.'pt-BR'.DIRECTORY_SEPARATOR.'messages.json',
            $locator->path('pt-BR', 'messages'),
        );

        self::assertSame(
            'translations'.DIRECTORY_SEPARATOR.'zh_Hant_TW'.DIRECTORY_SEPARATOR.'main.json',
            $locator->path('zh_Hant_TW'),
        );
    }

    #[DataProvider('unsafeSegments')]
    public function test_it_rejects_unsafe_path_segments(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TranslationCatalogLocator)->path($value);
    }

    public static function unsafeSegments(): array
    {
        return [
            'empty' => [''],
            'current directory' => ['.'],
            'parent directory' => ['..'],
            'forward slash' => ['../en'],
            'backslash' => ['..\\en'],
            'nested path' => ['en/subdir'],
            'null byte' => ["en\0json"],
        ];
    }
}
