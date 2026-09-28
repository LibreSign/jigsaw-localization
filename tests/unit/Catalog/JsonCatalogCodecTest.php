<?php

namespace Tests\Unit\Catalog;

use LibreSign\JigsawLocalization\Catalog\JsonCatalogCodec;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class JsonCatalogCodecTest extends TestCase
{
    public function test_it_decodes_flat_json_objects(): void
    {
        $codec = new JsonCatalogCodec;

        self::assertSame(
            ['Hello' => 'Olá', 'Path' => '/docs'],
            $codec->decode('{"Hello":"Olá","Path":"/docs"}'),
        );
    }

    public function test_it_encodes_deterministic_json_objects(): void
    {
        $codec = new JsonCatalogCodec;

        self::assertSame(
            "{\n    \"Alpha\": \"Ação\",\n    \"Zulu\": \"Último\"\n}\n",
            $codec->encode([
                'Zulu' => 'Último',
                'Alpha' => 'Ação',
            ]),
        );
    }

    public function test_empty_catalog_is_encoded_as_an_object(): void
    {
        self::assertSame("{}\n", (new JsonCatalogCodec)->encode([]));
    }

    #[DataProvider('invalidJsonCatalogs')]
    public function test_invalid_json_catalogs_are_rejected(string $contents, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        (new JsonCatalogCodec)->decode($contents, 'fixture.json');
    }

    public static function invalidJsonCatalogs(): array
    {
        return [
            'malformed JSON' => ['{invalid', 'Invalid JSON translation catalog'],
            'array root' => ['[]', 'must contain a JSON object'],
            'string root' => ['"value"', 'must contain a JSON object'],
            'nested value' => ['{"Hello":{"nested":"value"}}', 'only string keys and string values'],
            'numeric value' => ['{"Count":1}', 'only string keys and string values'],
            'null value' => ['{"Empty":null}', 'only string keys and string values'],
        ];
    }

    #[DataProvider('invalidValues')]
    public function test_non_string_values_are_rejected_when_encoding(mixed $value): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('only string values');

        /** @var array<string, string> $catalog */
        $catalog = ['Key' => $value];

        (new JsonCatalogCodec)->encode($catalog, 'fixture.json');
    }

    public static function invalidValues(): array
    {
        return [
            'integer' => [1],
            'boolean' => [true],
            'null' => [null],
            'array' => [['nested']],
        ];
    }
}
