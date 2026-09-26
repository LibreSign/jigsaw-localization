<?php

namespace ElaborateCode\JigsawLocalization\Loader;

use ElaborateCode\JigsawLocalization\Catalog\JsonCatalogCodec;
use ElaborateCode\JigsawLocalization\Catalog\MultiLocaleJsonCodec;
use ElaborateCode\JigsawLocalization\Contracts\LocalizationLoader;
use RuntimeException;

final class JsonDirectoryLoader implements LocalizationLoader
{
    private JsonCatalogCodec $catalogCodec;

    private MultiLocaleJsonCodec $multiLocaleCodec;

    public function __construct(
        private string $path = '/lang',
        private ?string $projectRoot = null,
        ?JsonCatalogCodec $catalogCodec = null,
        ?MultiLocaleJsonCodec $multiLocaleCodec = null,
    ) {
        $this->catalogCodec = $catalogCodec ?? new JsonCatalogCodec;
        $this->multiLocaleCodec = $multiLocaleCodec ?? new MultiLocaleJsonCodec;
    }

    public function load(): array
    {
        $localization = [];

        foreach ($this->localeDirectories() as $locale => $directory) {
            if (strcasecmp($locale, 'multi') === 0) {
                $this->loadMultiDirectory($directory, $localization);
                continue;
            }

            $localization[$locale] = ($localization[$locale] ?? []) + $this->loadLocaleDirectory($directory);
        }

        return $localization;
    }

    /**
     * @return array<string, string>
     */
    private function localeDirectories(): array
    {
        $directory = $this->resolveDirectory();
        $locales = [];

        foreach ($this->directoryEntries($directory) as $entry) {
            $path = $directory.DIRECTORY_SEPARATOR.$entry;
            if (is_dir($path)) {
                $locales[$entry] = $path;
            }
        }

        return $locales;
    }

    /**
     * @return array<string, string>
     */
    private function loadLocaleDirectory(string $directory): array
    {
        $translations = [];

        foreach ($this->jsonFiles($directory) as $file) {
            $translations += $this->catalogCodec->decode($this->read($file), $file);
        }

        return $translations;
    }

    /**
     * @param  array<string, array<string, string>>  $localization
     */
    private function loadMultiDirectory(string $directory, array &$localization): void
    {
        foreach ($this->jsonFiles($directory) as $file) {
            foreach ($this->multiLocaleCodec->decode($this->read($file), $file) as $locale => $translations) {
                $localization[$locale] = ($localization[$locale] ?? []) + $translations;
            }
        }
    }

    /**
     * @return list<string>
     */
    private function jsonFiles(string $directory): array
    {
        $files = [];

        foreach ($this->directoryEntries($directory) as $entry) {
            $path = $directory.DIRECTORY_SEPARATOR.$entry;
            if (is_file($path) && str_ends_with(strtolower($entry), '.json')) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * @return list<string>
     */
    private function directoryEntries(string $directory): array
    {
        $entries = scandir($directory);

        if ($entries === false) {
            throw new RuntimeException("Unable to read translation directory: {$directory}");
        }

        return array_values(array_diff($entries, ['.', '..']));
    }

    private function resolveDirectory(): string
    {
        if (is_dir($this->path)) {
            return $this->path;
        }

        $root = $this->projectRoot ?? getcwd();
        if ($root === false) {
            throw new RuntimeException('Unable to determine the project root.');
        }

        $candidate = rtrim($root, '/\\').DIRECTORY_SEPARATOR.ltrim($this->path, '/\\');

        if (! is_dir($candidate)) {
            throw new RuntimeException("Translation directory does not exist: {$this->path}");
        }

        return $candidate;
    }

    private function read(string $path): string
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read translation catalog: {$path}");
        }

        return $contents;
    }
}
