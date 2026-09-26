<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

/**
 * Produces catalogs without imposing project-specific ownership or workflow.
 */
final class TranslationCatalogSynchronizer
{
    /**
     * Build a canonical source catalog where each source string is both key and value.
     *
     * @param  iterable<string>  $sourceStrings
     * @return array<string, string>
     */
    public function source(iterable $sourceStrings): array
    {
        $catalog = [];

        foreach ($sourceStrings as $text) {
            $catalog[$text] = $text;
        }

        ksort($catalog);

        return $catalog;
    }

    /**
     * Synchronize a translated catalog with the current source keys.
     *
     * Existing translations are preserved. New source strings are initialized
     * with their source text. Obsolete translations are preserved by default;
     * callers may opt into pruning them.
     *
     * @param  array<string, string>  $source
     * @param  array<string, string>  $translation
     * @return array<string, string>
     */
    public function translation(
        array $source,
        array $translation,
        bool $pruneObsolete = false,
    ): array {
        $synchronized = $pruneObsolete ? [] : $translation;

        foreach ($source as $key => $sourceText) {
            $synchronized[$key] = $translation[$key] ?? $sourceText;
        }

        ksort($synchronized);

        return $synchronized;
    }
}
