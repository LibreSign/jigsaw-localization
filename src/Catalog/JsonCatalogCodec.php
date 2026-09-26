<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

use JsonException;
use RuntimeException;
use stdClass;

/**
 * Encodes and decodes the flat JSON object format used by translation catalogs.
 */
final class JsonCatalogCodec
{
    /**
     * @return array<string, string>
     */
    public function decode(string $contents, string $source = 'translation catalog'): array
    {
        try {
            $decoded = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid JSON translation catalog: {$source}", 0, $exception);
        }

        if (! $decoded instanceof stdClass) {
            throw new RuntimeException("Translation catalog must contain a JSON object: {$source}");
        }

        return $this->objectToTranslations($decoded, $source);
    }

    /**
     * @param  array<string, string>  $translations
     */
    public function encode(array $translations, string $source = 'translation catalog'): string
    {
        $this->assertStringValues($translations, $source);
        ksort($translations, SORT_STRING);

        try {
            return json_encode(
                (object) $translations,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
            )."\n";
        } catch (JsonException $exception) {
            throw new RuntimeException("Unable to encode translation catalog: {$source}", 0, $exception);
        }
    }

    /**
     * @return array<string, string>
     */
    private function objectToTranslations(stdClass $decoded, string $source): array
    {
        $translations = [];

        foreach (get_object_vars($decoded) as $key => $value) {
            if (! is_string($value)) {
                throw new RuntimeException("Translation catalog must contain only string keys and string values: {$source}");
            }

            $translations[$key] = $value;
        }

        return $translations;
    }

    /**
     * @param  array<string, string>  $translations
     */
    private function assertStringValues(array $translations, string $source): void
    {
        foreach ($translations as $value) {
            /** @var mixed $value */
            if (! is_string($value)) {
                throw new RuntimeException("Translation catalog must contain only string values: {$source}");
            }
        }
    }
}
