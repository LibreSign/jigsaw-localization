<?php

namespace ElaborateCode\JigsawLocalization;

use ElaborateCode\JsonTongue\TongueFacade;
use TightenCo\Jigsaw\Jigsaw;

class LoadLocalization
{
    public function __construct(private string $path = '/lang') {}

    public function handle(Jigsaw $jigsaw): void
    {
        $jigsaw->setConfig('localization', $this->load());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function load(): array
    {
        return (new TongueFacade($this->path))->transcribe();
    }
}
