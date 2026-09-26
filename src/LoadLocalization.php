<?php

namespace ElaborateCode\JigsawLocalization;

use ElaborateCode\JigsawLocalization\Contracts\LocalizationLoader;
use ElaborateCode\JigsawLocalization\Loader\JsonDirectoryLoader;
use TightenCo\Jigsaw\Jigsaw;

class LoadLocalization
{
    private LocalizationLoader $loader;

    public function __construct(
        string $path = '/lang',
        ?LocalizationLoader $loader = null,
    ) {
        $this->loader = $loader ?? new JsonDirectoryLoader($path);
    }

    public function handle(Jigsaw $jigsaw): void
    {
        $jigsaw->setConfig('localization', $this->load());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function load(): array
    {
        return $this->loader->load();
    }
}
