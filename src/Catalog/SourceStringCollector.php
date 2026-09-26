<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

use InvalidArgumentException;

/**
 * Collects canonical source strings for one locale.
 *
 * The collector is deliberately unaware of translation services and storage.
 * Consumers decide when and where the collected catalog is persisted.
 */
final class SourceStringCollector
{
    /** @var array<string, string> */
    private array $strings = [];

    public function __construct(private string $sourceLocale = 'en')
    {
        if ($sourceLocale === '') {
            throw new InvalidArgumentException('The source locale cannot be empty.');
        }
    }

    public function collect(string $locale, string $text): void
    {
        if ($locale !== $this->sourceLocale) {
            return;
        }

        $this->strings[$text] = $text;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        $strings = $this->strings;
        ksort($strings);

        return $strings;
    }

    public function clear(): void
    {
        $this->strings = [];
    }
}
