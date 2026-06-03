<?php

/**
 * @see https://www.w3.org/International/articles/language-tags/
 * @see https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry
 *
 * @note a path always starts with '/' and doesn't end with it
 */

/**
 * @param  mixed  $page
 * @return string The translated text if found, else returns the same given $text
 */
function __($page, string $text, ?string $current_locale = null): string
{
    $current_locale ??= current_path_locale($page);

    return $page->localization[$current_locale][$text] ?? $text;
}

// ! The following helpers relies on the locale folder structure

/**
 * @param  mixed  $page
 */
function current_path_locale($page): string
{
    $path = trim($page->getPath(), '/');

    /**
     * - [a-z]{2,3} language code
     * - [A-Z]{2} region code
     *
     * @var string $locale_regex
     */
    $locale_regex = '/^(?<locale>(?:[a-z]{2,3}-[A-Z]{2})|(?:[a-z]{2,3}))(?:[^a-zA-Z]|$)/';

    preg_match($locale_regex, $path, $matches);

    return $matches['locale'] ?? packageDefaultLocale();
}

/**
 * @param  mixed  $page
 * @param  ?string  $target_locale  set to the default locale if null
 * @return string Places $target_locale code in the current path
 */
function translate_path($page, ?string $target_locale = null): string
{
    $target_locale ??= packageDefaultLocale();

    $current_locale = current_path_locale($page);

    $partial_path = (string) match (true) {
        $current_locale === packageDefaultLocale($page) => $page->getPath(),
        default => substr($page->getPath(), strlen($current_locale) + 1),
    };
    if ($partial_path === '/') {
        $partial_path = '';
    }

    $match = match (true) {
        $target_locale === packageDefaultLocale($page) => $partial_path,
        default => "/{$target_locale}".($partial_path === '/' ? '' : $partial_path),
    };

    return ! empty($match) ? $match : '/';
}

/**
 * @param  mixed  $page
 * @param  ?string  $target_locale  set to the default locale if null
 * @return string Places $target_locale code in the current url
 */
function translate_url($page, ?string $target_locale = null): string
{
    return url(translate_path($page, $target_locale));
}

/**
 * @param  mixed  $page
 * @param  string  $partial_path  A path without the language prefix
 * @param  ?string  $target_locale  uses the default locale if null
 * @return string A path on the target locale
 */
function locale_path($page, string $partial_path, ?string $target_locale = null): string
{
    $target_locale ??= current_path_locale($page);

    $partial_path = '/'.ltrim($partial_path, '/');
    $partial_path = preg_replace("/^\/$target_locale\//", '/', $partial_path);
    if ($partial_path === '/') {
        $partial_path = '';
    }

    $match = match (true) {
        $target_locale === packageDefaultLocale($page) => $partial_path,
        default => "/{$target_locale}".($partial_path === '/' ? '' : $partial_path),
    };

    return ! empty($match) ? $match : '/';
}

/**
 * @param  mixed  $page
 * @param  string  $partial_path  A path without the language prefix
 * @param  ?string  $target_locale  uses the default locale if null
 * @return string A URL on the target locale
 */
function locale_url($page, string $partial_path, ?string $target_locale = null): string
{
    return url(locale_path($page, $partial_path, $target_locale));
}

// ===========================================
function packageDefaultLocale($page = null): string
{
    return $page->defaultLocale ?? 'en';
}

/**
 * Returns the display name map for all supported locales.
 * Falls back to the locale code if not found.
 *
 * Site-level overrides can be provided via a `localeNames` config key.
 *
 * @param  mixed  $page
 * @return array<string, string>  locale code => display name
 */
function locale_names($page): array
{
    static $defaults = [
        'en'    => 'English',
        'cs'    => 'Čeština',
        'fr'    => 'Français',
        'nb-NO' => 'Norsk bokmål',
        'pt'    => 'Português',
        'pt-BR' => 'Português Brasil',
        'ta'    => 'தமிழ்',
    ];

    if (isset($page->localeNames) && is_array($page->localeNames)) {
        return $page->localeNames;
    }

    return $defaults;
}

/**
 * Returns an ordered map of URL key => display name for all available locales.
 * The default locale uses an empty string key (no URL prefix).
 *
 * Intended for use in navigation language selectors.
 *
 * @param  mixed  $page
 * @return array<string, string>  url key => display name
 */
function available_locales($page): array
{
    $names = locale_names($page);

    return $page->localization->keys()
        ->mapWithKeys(function ($locale) use ($page, $names) {
            $urlKey = ($locale === packageDefaultLocale($page)) ? '' : $locale;
            $name = $names[$locale] ?? $locale;

            return [$urlKey => $name];
        })
        ->all();
}
