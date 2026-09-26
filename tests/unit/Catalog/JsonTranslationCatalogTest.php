<?php

namespace Tests\Unit\Catalog;

use ElaborateCode\JigsawLocalization\Catalog\JsonTranslationCatalog;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class JsonTranslationCatalogTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/jigsaw-localization-'.bin2hex(random_bytes(8));
        mkdir($this->directory, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);

        parent::tearDown();
    }

    public function test_missing_catalog_reads_as_empty(): void
    {
        $catalog = new JsonTranslationCatalog($this->directory.'/missing.json');

        self::assertSame([], $catalog->read());
    }

    public function test_it_writes_deterministic_unicode_json_and_reads_it_back(): void
    {
        $path = $this->directory.'/pt-BR/main.json';
        $catalog = new JsonTranslationCatalog($path);

        self::assertTrue($catalog->write([
            'Zulu' => 'Último',
            'Alpha' => 'Ação',
        ]));

        self::assertSame([
            'Alpha' => 'Ação',
            'Zulu' => 'Último',
        ], $catalog->read());

        self::assertSame(
            "{\n    \"Alpha\": \"Ação\",\n    \"Zulu\": \"Último\"\n}\n",
            file_get_contents($path),
        );
    }

    public function test_empty_catalog_is_written_as_a_json_object(): void
    {
        $path = $this->directory.'/en/main.json';
        $catalog = new JsonTranslationCatalog($path);

        self::assertTrue($catalog->write([]));
        self::assertSame("{}\n", file_get_contents($path));
        self::assertSame([], $catalog->read());
    }

    public function test_numeric_keys_still_produce_a_json_object(): void
    {
        $path = $this->directory.'/en/main.json';
        $catalog = new JsonTranslationCatalog($path);

        $catalog->write([0 => 'Zero']);

        self::assertSame("{\n    \"0\": \"Zero\"\n}\n", file_get_contents($path));
    }

    public function test_non_string_values_are_rejected_before_writing(): void
    {
        $catalog = new JsonTranslationCatalog($this->directory.'/en/main.json');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('only string values');

        $catalog->write(['Hello' => ['nested']]);
    }

    public function test_write_reports_when_content_is_already_current(): void
    {
        $catalog = new JsonTranslationCatalog($this->directory.'/en/main.json');
        $translations = ['Hello' => 'Hello'];

        self::assertTrue($catalog->write($translations));
        self::assertFalse($catalog->write($translations));
    }

    public function test_invalid_json_is_rejected(): void
    {
        $path = $this->directory.'/invalid.json';
        file_put_contents($path, '{invalid');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON translation catalog');

        (new JsonTranslationCatalog($path))->read();
    }

    public function test_json_arrays_are_rejected_even_when_empty(): void
    {
        $path = $this->directory.'/invalid.json';
        file_put_contents($path, '[]');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('must contain a JSON object');

        (new JsonTranslationCatalog($path))->read();
    }

    public function test_nested_or_non_string_values_are_rejected(): void
    {
        $path = $this->directory.'/invalid.json';
        file_put_contents($path, '{"Hello":{"nested":"value"}}');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('only string keys and string values');

        (new JsonTranslationCatalog($path))->read();
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory) ?: [], ['.', '..']);
        foreach ($items as $item) {
            $path = $directory.DIRECTORY_SEPARATOR.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
