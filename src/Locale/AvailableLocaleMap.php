<?php

namespace LibreSign\JigsawLocalization\Locale;

/**
 * Builds the URL-key/display-name map used by locale navigation.
 */
final class AvailableLocaleMap
{
    /**
     * @param  iterable<string>  $locales
     * @param  array<string, string>  $names
     * @return array<string, string>
     */
    public function build(iterable $locales, string $defaultLocale, array $names): array
    {
        $available = [];

        foreach ($locales as $locale) {
            $urlKey = $locale === $defaultLocale ? '' : $locale;
            $available[$urlKey] = $names[$locale] ?? $locale;
        }

        return $available;
    }
}
