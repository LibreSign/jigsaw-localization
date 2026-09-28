<?php

namespace Tests\Unit\Loader;

use LibreSign\JigsawLocalization\Loader\JsonDirectoryLoader;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class JsonDirectoryLoaderTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/jigsaw-localization-loader-'.bin2hex(random_bytes(8));
        mkdir($this->directory, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);

        parent::tearDown();
    }

    public function test_it_loads_and_merges_locale_json_files(): void
    {
        $this->write('en/01-main.json', '{"Hello":"Hello","Shared":"First"}');
        $this->write('en/02-extra.json', '{"Goodbye":"Goodbye","Shared":"Second"}');
        $this->write('pt-BR/main.json', '{"Hello":"Olá"}');
        $this->write('pt-BR/notes.txt', 'ignored');

        self::assertSame([
            'en' => [
                'Hello' => 'Hello',
                'Shared' => 'First',
                'Goodbye' => 'Goodbye',
            ],
            'pt-BR' => [
                'Hello' => 'Olá',
            ],
        ], (new JsonDirectoryLoader($this->directory))->load());
    }

    public function test_it_preserves_legacy_multi_locale_catalogs(): void
    {
        $this->write('multi/common.json', '{"en":{"Hello":"Hello"},"es":{"Hello":"Hola"}}');

        self::assertSame([
            'en' => ['Hello' => 'Hello'],
            'es' => ['Hello' => 'Hola'],
        ], (new JsonDirectoryLoader($this->directory))->load());
    }

    public function test_first_definition_wins_across_regular_and_multi_catalogs(): void
    {
        $this->write('en/main.json', '{"Hello":"Regular"}');
        $this->write('multi/common.json', '{"en":{"Hello":"Multi","Goodbye":"Bye"}}');
        $this->write('pt-BR/main.json', '{"Hello":"Regular PT"}');
        $this->write('multi/other.json', '{"pt-BR":{"Hello":"Multi PT","Goodbye":"Tchau"}}');

        self::assertSame([
            'en' => [
                'Hello' => 'Regular',
                'Goodbye' => 'Bye',
            ],
            'pt-BR' => [
                'Hello' => 'Multi PT',
                'Goodbye' => 'Tchau',
            ],
        ], (new JsonDirectoryLoader($this->directory))->load());
    }

    public function test_relative_package_path_can_be_resolved_from_project_root(): void
    {
        $this->write('lang/en/main.json', '{"Hello":"Hello"}');

        $loader = new JsonDirectoryLoader('/lang', $this->directory);

        self::assertSame(
            ['en' => ['Hello' => 'Hello']],
            $loader->load(),
        );
    }

    public function test_missing_translation_directory_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Translation directory does not exist');

        (new JsonDirectoryLoader('/missing', $this->directory))->load();
    }

    public function test_invalid_json_is_not_silently_treated_as_empty(): void
    {
        $this->write('en/main.json', '{invalid');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON translation catalog');

        (new JsonDirectoryLoader($this->directory))->load();
    }

    private function write(string $relativePath, string $contents): void
    {
        $path = $this->directory.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $contents);
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (array_diff(scandir($directory) ?: [], ['.', '..']) as $item) {
            $path = $directory.DIRECTORY_SEPARATOR.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
