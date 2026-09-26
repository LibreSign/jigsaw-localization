<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

use JsonException;
use RuntimeException;
use stdClass;

final class MultiLocaleJsonCodec
{
    /**
     * @return array<string, array<string, string>>
     */
    public function decode(string $contents, string $source = 'translation catalog'): array
    {
        try {
            $decoded = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid JSON translation catalog: {$source}", 0, $exception);
        }

        return $this->decodeObject($decoded, $source);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function decodeObject(mixed $decoded, string $source): array
    {
        if (! $decoded instanceof stdClass) {
            throw new RuntimeException("Multi-locale catalog must contain a JSON object: {$source}");
        }

        $locales = [];

        foreach (get_object_vars($decoded) as $locale => $translations) {
            $locales[$locale] = $this->decodeLocale($translations, $source);
        }

        return $locales;
    }

    /**
     * @return array<string, string>
     */
    private function decodeLocale(mixed $translations, string $source): array
    {
        if (! $translations instanceof stdClass) {
            throw new RuntimeException("Multi-locale catalog entries must be JSON objects: {$source}");
        }

        $catalog = [];

        foreach (get_object_vars($translations) as $key => $value) {
            if (! is_string($value)) {
                throw new RuntimeException("Translation catalog must contain only string values: {$source}");
            }

            $catalog[$key] = $value;
        }

        return $catalog;
    }
}
