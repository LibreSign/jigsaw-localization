<?php

namespace Tests\Unit;

use ElaborateCode\JigsawLocalization\Contracts\LocalizationLoader;
use ElaborateCode\JigsawLocalization\LoadLocalization;
use PHPUnit\Framework\TestCase;

final class LocalizationLoaderContractTest extends TestCase
{
    public function test_load_localization_accepts_custom_loader_implementations(): void
    {
        $loader = new class implements LocalizationLoader
        {
            public function load(): array
            {
                return [
                    'custom' => [
                        'Hello' => 'Custom translation',
                    ],
                ];
            }
        };

        $localization = (new LoadLocalization(loader: $loader))->load();

        self::assertSame([
            'custom' => [
                'Hello' => 'Custom translation',
            ],
        ], $localization);
    }
}
