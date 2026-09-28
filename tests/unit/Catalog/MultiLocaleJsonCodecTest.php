<?php

namespace Tests\Unit\Catalog;

use LibreSign\JigsawLocalization\Catalog\MultiLocaleJsonCodec;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MultiLocaleJsonCodecTest extends TestCase
{
    public function test_it_decodes_multi_locale_catalogs(): void
    {
        $catalogs = (new MultiLocaleJsonCodec)->decode(
            '{"en":{"Hello":"Hello"},"pt-BR":{"Hello":"Olá"}}',
        );

        self::assertSame([
            'en' => ['Hello' => 'Hello'],
            'pt-BR' => ['Hello' => 'Olá'],
        ], $catalogs);
    }

    #[DataProvider('invalidCatalogs')]
    public function test_invalid_multi_locale_catalogs_are_rejected(string $json, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        (new MultiLocaleJsonCodec)->decode($json, 'multi.json');
    }

    public static function invalidCatalogs(): array
    {
        return [
            'invalid JSON' => ['{invalid', 'Invalid JSON translation catalog'],
            'array root' => ['[]', 'must contain a JSON object'],
            'locale is not an object' => ['{"en":"Hello"}', 'entries must be JSON objects'],
            'translation is not a string' => ['{"en":{"Count":1}}', 'only string values'],
        ];
    }
}
