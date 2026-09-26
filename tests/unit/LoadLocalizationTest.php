<?php

namespace Tests\Unit;

use ElaborateCode\JigsawLocalization\LoadLocalization;
use PHPUnit\Framework\TestCase;
use TightenCo\Jigsaw\Container;

final class LoadLocalizationTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().'/jigsaw-localization-loader-'.bin2hex(random_bytes(8));
        mkdir($this->directory.'/en', 0755, true);
        mkdir($this->directory.'/pt-BR', 0755, true);

        file_put_contents($this->directory.'/en/main.json', '{"Hello":"Hello"}');
        file_put_contents($this->directory.'/pt-BR/main.json', '{"Hello":"Olá"}');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->directory);

        parent::tearDown();
    }

    public function test_it_loads_localization_from_a_custom_directory(): void
    {
        $localization = (new LoadLocalization($this->directory))->load();

        self::assertSame('Hello', $localization['en']['Hello']);
        self::assertSame('Olá', $localization['pt-BR']['Hello']);
    }

    public function test_jigsaw_container_can_resolve_the_default_listener(): void
    {
        $listener = Container::getInstance()->make(LoadLocalization::class);

        self::assertInstanceOf(LoadLocalization::class, $listener);
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
