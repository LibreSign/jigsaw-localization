<?php

namespace Tests\Unit\Locale;

use LibreSign\JigsawLocalization\Locale\LocaleNameResolver;
use PHPUnit\Framework\TestCase;

final class LocaleNameResolverTest extends TestCase
{
    public function test_it_resolves_names_through_an_injected_strategy(): void
    {
        $resolver = new LocaleNameResolver(
            static fn (string $locale): string => strtoupper($locale),
        );

        self::assertSame([
            'en' => 'EN',
            'pt-BR' => 'PT-BR',
        ], $resolver->names(['en', 'pt-BR']));
    }

    public function test_project_overrides_bypass_display_name_resolution(): void
    {
        $calls = 0;
        $resolver = new LocaleNameResolver(
            static function (string $locale) use (&$calls): string {
                $calls++;

                return $locale;
            },
        );

        self::assertSame(
            ['pt-BR' => 'Português'],
            $resolver->names(['en', 'pt-BR'], ['pt-BR' => 'Português']),
        );
        self::assertSame(0, $calls);
    }
}
