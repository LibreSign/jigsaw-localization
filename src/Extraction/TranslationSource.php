<?php

namespace ElaborateCode\JigsawLocalization\Extraction;

use InvalidArgumentException;

/**
 * A locale-aware unit that can be inspected for translatable strings.
 *
 * The identifier can be a file path, URI, template name, database key, or any
 * other stable reference chosen by the consuming project.
 */
final class TranslationSource
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        private string $locale,
        private string $identifier,
        private string $contents,
        private array $metadata = [],
    ) {
        if ($locale === '') {
            throw new InvalidArgumentException('The source locale cannot be empty.');
        }

        if ($identifier === '') {
            throw new InvalidArgumentException('The source identifier cannot be empty.');
        }
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function identifier(): string
    {
        return $this->identifier;
    }

    public function contents(): string
    {
        return $this->contents;
    }

    /**
     * @return array<string, mixed>
     */
    public function metadata(): array
    {
        return $this->metadata;
    }
}
