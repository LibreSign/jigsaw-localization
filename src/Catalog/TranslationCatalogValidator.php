<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

/**
 * Performs format-level checks that are useful regardless of how a project
 * creates translations.
 */
final class TranslationCatalogValidator
{
    private PlaceholderExtractor $placeholderExtractor;

    public function __construct(?PlaceholderExtractor $placeholderExtractor = null)
    {
        $this->placeholderExtractor = $placeholderExtractor ?? new PlaceholderExtractor;
    }

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
            if (! array_key_exists($key, $source)) {
                continue;
            }

            $error = $this->placeholderError($key, $source[$key], $translatedText);
            if ($error !== null) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
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

    private function placeholderError(string $key, string $source, string $translation): ?string
    {
        $sourcePlaceholders = $this->placeholderExtractor->extract($source);
        $translationPlaceholders = $this->placeholderExtractor->extract($translation);

        if ($sourcePlaceholders === $translationPlaceholders) {
            return null;
        }

        return sprintf(
            'Placeholder mismatch for "%s": expected [%s], got [%s].',
            $key,
            implode(', ', $sourcePlaceholders),
            implode(', ', $translationPlaceholders),
        );
    }
}
