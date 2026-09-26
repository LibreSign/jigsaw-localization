<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

use JsonException;
use RuntimeException;

/**
 * Reads and writes a flat JSON translation catalog.
 *
 * This class does not decide who owns the file or how translations are
 * produced. Files can be maintained manually or by any external tool.
 */
final class JsonTranslationCatalog
{
    public function __construct(private readonly string $path)
    {
    }

    /**
     * @return array<string, string>
     */
    public function read(): array
    {
        if (! is_file($this->path)) {
            return [];
        }

        $contents = file_get_contents($this->path);
        if ($contents === false) {
            throw new RuntimeException("Unable to read translation catalog: {$this->path}");
        }

        try {
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid JSON translation catalog: {$this->path}", 0, $exception);
        }

        if (! is_array($decoded)) {
            throw new RuntimeException("Translation catalog must contain a JSON object: {$this->path}");
        }

        foreach ($decoded as $key => $value) {
            if (! is_string($key) || ! is_string($value)) {
                throw new RuntimeException("Translation catalog must contain only string keys and string values: {$this->path}");
            }
        }

        /** @var array<string, string> $decoded */
        return $decoded;
    }

    /**
     * Persist a deterministic representation of a flat translation catalog.
     *
     * @param  array<string, string>  $translations
     * @return bool true when the file changed, false when it was already current
     */
    public function write(array $translations): bool
    {
        ksort($translations);

        $directory = dirname($this->path);
        if (is_dir($directory) === false) {
            $created = mkdir($directory, 0755, true);
            if ($created === false && is_dir($directory) === false) {
                throw new RuntimeException("Unable to create translation catalog directory: {$directory}");
            }
        }

        try {
            $encoded = json_encode(
                $translations,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
            )."\n";
        } catch (JsonException $exception) {
            throw new RuntimeException("Unable to encode translation catalog: {$this->path}", 0, $exception);
        }

        if (is_file($this->path) && file_get_contents($this->path) === $encoded) {
            return false;
        }

        if (file_put_contents($this->path, $encoded, LOCK_EX) === false) {
            throw new RuntimeException("Unable to write translation catalog: {$this->path}");
        }

        return true;
    }
}
