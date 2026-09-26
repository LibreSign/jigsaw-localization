<?php

namespace LibreSign\JigsawLocalization\Extraction;

use InvalidArgumentException;

final class ExtractedString
{
    public function __construct(
        private string $text,
        private string $source,
        private ?int $line = null,
        private ?string $context = null,
    ) {
        $this->assertNotEmpty($text, 'Extracted translation text cannot be empty.');
        $this->assertNotEmpty($source, 'Extracted translation source cannot be empty.');
        $this->assertValidLine($line);
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

    private function assertNotEmpty(string $value, string $message): void
    {
        if ($value === '') {
            throw new InvalidArgumentException($message);
        }
    }

    private function assertValidLine(?int $line): void
    {
        if (($line ?? 1) < 1) {
            throw new InvalidArgumentException('The source line must be greater than zero.');
        }
    }
}
