<?php

namespace ElaborateCode\JigsawLocalization\Contracts;

interface LocalizationLoader
{
    /**
     * @return array<string, array<string, string>>
     */
    public function load(): array;
}
