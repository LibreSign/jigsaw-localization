<?php

namespace ElaborateCode\JigsawLocalization\Extraction;

use InvalidArgumentException;

final class ExtractedString
{
    public function __construct(
        private string $text,
        private string $source,
        private ?int $line = null,
        private ?string $context = null,
    ) {
        if ($text === '') {
            throw new InvalidArgumentException('Extracted translation text cannot be empty.');
        }

        if ($source === '') {
            throw new InvalidArgumentException('Extracted translation source cannot be empty.');
        }

        if ($line !== null && $line < 1) {
            throw new InvalidArgumentException('The source line must be greater than zero.');
        }
    }

    public function text(): string
    {
        return $this->text;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function line(): ?int
    {
        return $this->line;
    }

    public function context(): ?string
    {
        return $this->context;
    }
}
