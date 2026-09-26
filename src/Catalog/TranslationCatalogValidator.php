<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

/**
 * Performs format-level checks that are useful regardless of how a project
 * creates translations.
 */
final class TranslationCatalogValidator
{
    /**
     * Validate printf/vsprintf placeholders for entries present in both catalogs.
     * Missing or extra translation keys are intentionally not treated as errors.
     *
     * @param  array<string, string>  $source
     * @param  array<string, string>  $translation
     * @return list<string>
     */
    public function validatePlaceholders(array $source, array $translation): array
    {
        $errors = [];

        foreach ($translation as $key => $translatedText) {
            if (!array_key_exists($key, $source)) {
                continue;
            }

            $sourcePlaceholders = $this->placeholders($source[$key]);
            $translationPlaceholders = $this->placeholders($translatedText);

            if ($sourcePlaceholders !== $translationPlaceholders) {
                $errors[] = sprintf(
                    'Placeholder mismatch for "%s": expected [%s], got [%s].',
                    $key,
                    implode(', ', $sourcePlaceholders),
                    implode(', ', $translationPlaceholders),
                );
            }
        }

        return $errors;
    }

    /**
     * Validate a canonical source catalog where source text is both key and value.
     *
     * @param  array<string, string>  $source
     * @return list<string>
     */
    public function validateCanonicalSource(array $source): array
    {
        $errors = [];

        foreach ($source as $key => $value) {
            if ($key !== $value) {
                $errors[] = sprintf('Canonical source value must match its key: "%s".', $key);
            }
        }

        return $errors;
    }

    /**
     * Convert printf placeholders to argument-position/type tokens so translators
     * may reorder arguments safely by using positional placeholders.
     *
     * @return list<string>
     */
    private function placeholders(string $text): array
    {
        $text = str_replace('%%', '', $text);

        preg_match_all(
            "/%(?!%)(?:(?<position>\\d+)\\$)?[-+0' #]*(?:\\d+)?(?:\\.\\d+)?(?<type>[bcdeEfFgGosuxX])/",
            $text,
            $matches,
            PREG_SET_ORDER,
        );

        $implicitPosition = 1;
        $placeholders = [];

        foreach ($matches as $match) {
            $position = $match['position'] !== '' ? (int) $match['position'] : $implicitPosition++;
            $placeholders[] = $position.':'.$match['type'];
        }

        sort($placeholders);

        return $placeholders;
    }
}
