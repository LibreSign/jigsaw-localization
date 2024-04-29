<?php

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class TransHelperTest extends TestCase
{
    #[DataProvider('provider__')]
    public function test__(string $text, ?string $locale, string $expected): void
    {
        $this->pageData
            ->setLocalization([
                'es' => [
                    'Cat' => 'Gato',
                ],
            ]);
        $actual = __($this->pageData, $text, $locale);
        $this->assertEquals($expected, $actual);
    }

    public static function provider__(): array
    {
        return [
            ['Cat', 'es', 'Gato'],
            ['Dog', 'es', 'Dog'],
        ];
    }
}
