<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

use InvalidArgumentException;

/**
 * Builds safe translation catalog paths without imposing a locale naming policy.
 *
 * Locale and catalog names are treated as path segments. Directory traversal
 * and nested segments are rejected, while projects remain free to use their
 * own locale naming convention.
 */
final class TranslationCatalogLocator
{
    public function __construct(private string $baseDirectory = 'lang')
    {
        if ($baseDirectory === '') {
            throw new InvalidArgumentException('The translation base directory cannot be empty.');
        }
    }

    public function path(string $locale, string $catalog = 'main'): string
    {
        $this->assertPathSegment($locale, 'locale');
        $this->assertPathSegment($catalog, 'catalog');

        $base = rtrim($this->baseDirectory, '/\\');

        return $base.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$catalog.'.json';
    }

    private function assertPathSegment(string $value, string $name): void
    {
        if (
            $value === ''
            || $value === '.'
            || $value === '..'
            || str_contains($value, '/')
            || str_contains($value, '\\')
            || str_contains($value, "\0")
        ) {
            throw new InvalidArgumentException(sprintf(
                'The translation %s must be a single safe path segment.',
                $name,
            ));
        }
    }
}
