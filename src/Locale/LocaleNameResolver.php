<?php

namespace LibreSign\JigsawLocalization\Locale;

use Closure;
use Symfony\Component\Intl\Locales;

/**
 * Resolves display names without coupling callers to Symfony Intl availability.
 */
final class LocaleNameResolver
{
    private Closure $displayName;

    /**
     * @param  null|callable(string): string  $displayName
     */
    public function __construct(?callable $displayName = null)
    {
        $displayName ??= static function (string $locale): string {
            if (! extension_loaded('intl')) {
                return $locale;
            }

            $icu = str_replace('-', '_', $locale);

            return Locales::exists($icu)
                ? Locales::getName($icu, $icu)
                : $locale;
        };

        $this->displayName = Closure::fromCallable($displayName);
    }

    /**
     * @param  iterable<string>  $locales
     * @param  null|array<string, string>  $overrides
     * @return array<string, string>
     */
    public function names(iterable $locales, ?array $overrides = null): array
    {
        if ($overrides !== null) {
            return $overrides;
        }

        $names = [];

        foreach ($locales as $locale) {
            $names[$locale] = ($this->displayName)($locale);
        }

        return $names;
    }
}
