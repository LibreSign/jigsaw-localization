<?php

namespace LibreSign\JigsawLocalization\Catalog;

use RuntimeException;

/**
 * Reads and writes a flat JSON translation catalog.
 *
 * This class owns filesystem concerns only. JSON format rules live in
 * JsonCatalogCodec so they can be tested without disk IO.
 */
final class JsonTranslationCatalog
{
    private JsonCatalogCodec $codec;

    public function __construct(
        private string $path,
        ?JsonCatalogCodec $codec = null,
    ) {
        $this->codec = $codec ?? new JsonCatalogCodec;
    }

    /**
     * @return array<string, string>
     */
    public function read(): array
    {
        if (! is_file($this->path)) {
            return [];
        }

        return $this->codec->decode($this->readContents(), $this->path);
    }

    /**
     * @param  array<string, string>  $translations
     * @return bool true when the file changed, false when it was already current
     */
    public function write(array $translations): bool
    {
        $encoded = $this->codec->encode($translations, $this->path);

        if ($this->isCurrent($encoded)) {
            return false;
        }

        $this->ensureDirectoryExists();
        $this->persist($encoded);

        return true;
    }

    private function readContents(): string
    {
        $contents = file_get_contents($this->path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read translation catalog: {$this->path}");
        }

        return $contents;
    }

    private function isCurrent(string $encoded): bool
    {
        return is_file($this->path)
            && file_get_contents($this->path) === $encoded;
    }

    private function ensureDirectoryExists(): void
    {
        $directory = dirname($this->path);

        if (is_dir($directory)) {
            return;
        }

        if (mkdir($directory, 0755, true) === false && ! is_dir($directory)) {
            throw new RuntimeException("Unable to create translation catalog directory: {$directory}");
        }
    }

    private function persist(string $contents): void
    {
        if (file_put_contents($this->path, $contents, LOCK_EX) === false) {
            throw new RuntimeException("Unable to write translation catalog: {$this->path}");
        }
    }
}
