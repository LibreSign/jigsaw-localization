<?php

namespace ElaborateCode\JigsawLocalization\Locale;

use InvalidArgumentException;

/**
 * Resolves locale-prefixed paths independently from Jigsaw page objects.
 */
final class LocalePathResolver
{
    /** @var array<string, true> */
    private array $supportedLocales = [];

    /**
     * @param  iterable<string>  $supportedLocales
     */
    public function __construct(
        private string $defaultLocale,
        iterable $supportedLocales,
    ) {
        if ($defaultLocale === '') {
            throw new InvalidArgumentException('The default locale cannot be empty.');
        }

        foreach ($supportedLocales as $locale) {
            if ($locale !== '') {
                $this->supportedLocales[$locale] = true;
            }
        }

        $this->supportedLocales[$defaultLocale] = true;
    }

    public function currentLocale(string $path): string
    {
        $firstSegment = $this->firstSegment($path);

        return isset($this->supportedLocales[$firstSegment])
            ? $firstSegment
            : $this->defaultLocale;
    }

    public function translate(string $path, ?string $targetLocale = null): string
    {
        $targetLocale ??= $this->defaultLocale;

        return $this->withLocalePrefix(
            $this->withoutCurrentLocalePrefix($path),
            $targetLocale,
        );
    }

    public function localize(string $partialPath, string $targetLocale): string
    {
        return $this->withLocalePrefix(
            $this->withoutLocalePrefix($this->normalizePath($partialPath), $targetLocale),
            $targetLocale,
        );
    }

    private function withoutCurrentLocalePrefix(string $path): string
    {
        $normalized = $this->normalizePath($path);
        $currentLocale = $this->currentLocale($normalized);

        return $currentLocale === $this->defaultLocale
            ? $normalized
            : $this->withoutLocalePrefix($normalized, $currentLocale);
    }

    private function withoutLocalePrefix(string $path, string $locale): string
    {
        if ($path === '/'.$locale) {
            return '/';
        }

        $prefix = '/'.$locale.'/';

        return str_starts_with($path, $prefix)
            ? '/'.substr($path, strlen($prefix))
            : $path;
    }

    private function withLocalePrefix(string $path, string $locale): string
    {
        $normalized = $this->normalizePath($path);

        if ($locale === $this->defaultLocale) {
            return $normalized;
        }

        return $normalized === '/'
            ? '/'.$locale
            : '/'.$locale.$normalized;
    }

    private function normalizePath(string $path): string
    {
        return '/'.ltrim($path, '/');
    }

    private function firstSegment(string $path): string
    {
        $trimmed = trim($path, '/');

        if ($trimmed === '') {
            return '';
        }

        $separator = strpos($trimmed, '/');

        return $separator === false
            ? $trimmed
            : substr($trimmed, 0, $separator);
    }
}
