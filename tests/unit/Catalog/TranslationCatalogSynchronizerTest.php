<?php

namespace Tests\Unit\Catalog;

use LibreSign\JigsawLocalization\Catalog\TranslationCatalogSynchronizer;
use PHPUnit\Framework\TestCase;

final class TranslationCatalogSynchronizerTest extends TestCase
{
    private TranslationCatalogSynchronizer $synchronizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->synchronizer = new TranslationCatalogSynchronizer;
    }

    public function test_source_catalog_is_canonical_deduplicated_and_sorted(): void
    {
        self::assertSame([
            'Alpha' => 'Alpha',
            'Zulu' => 'Zulu',
        ], $this->synchronizer->source(['Zulu', 'Alpha', 'Zulu']));
    }

    public function test_translation_preserves_existing_values_and_initializes_new_keys(): void
    {
        $source = [
            'Goodbye' => 'Goodbye',
            'Hello' => 'Hello',
        ];

        self::assertSame([
            'Goodbye' => 'Goodbye',
            'Hello' => 'Olá',
        ], $this->synchronizer->translation($source, ['Hello' => 'Olá']));
    }

    public function test_obsolete_translation_keys_are_preserved_by_default(): void
    {
        self::assertSame([
            'Hello' => 'Olá',
            'Old' => 'Antigo',
        ], $this->synchronizer->translation(
            ['Hello' => 'Hello'],
            ['Hello' => 'Olá', 'Old' => 'Antigo'],
        ));
    }

    public function test_obsolete_translation_keys_can_be_pruned_explicitly(): void
    {
        self::assertSame(
            ['Hello' => 'Olá'],
            $this->synchronizer->translation(
                ['Hello' => 'Hello'],
                ['Hello' => 'Olá', 'Old' => 'Antigo'],
                true,
            ),
        );
    }

    public function test_source_values_do_not_overwrite_existing_translation(): void
    {
        self::assertSame(
            ['Hello' => 'Olá'],
            $this->synchronizer->translation(
                ['Hello' => 'Hello'],
                ['Hello' => 'Olá'],
            ),
        );
    }
}
