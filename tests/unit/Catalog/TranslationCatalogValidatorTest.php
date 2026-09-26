<?php

namespace Tests\Unit\Catalog;

use ElaborateCode\JigsawLocalization\Catalog\TranslationCatalogValidator;
use PHPUnit\Framework\TestCase;

final class TranslationCatalogValidatorTest extends TestCase
{
    private TranslationCatalogValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TranslationCatalogValidator;
    }

    public function test_matching_placeholders_are_valid(): void
    {
        $errors = $this->validator->validatePlaceholders(
            ['By %s' => 'By %s'],
            ['By %s' => 'Por %s'],
        );

        self::assertSame([], $errors);
    }

    public function test_positional_placeholders_can_be_reordered(): void
    {
        $errors = $this->validator->validatePlaceholders(
            ['%s signed %d documents' => '%s signed %d documents'],
            ['%s signed %d documents' => '%2$d documentos assinados por %1$s'],
        );

        self::assertSame([], $errors);
    }

    public function test_missing_placeholder_is_reported(): void
    {
        $errors = $this->validator->validatePlaceholders(
            ['By %s' => 'By %s'],
            ['By %s' => 'Por'],
        );

        self::assertCount(1, $errors);
        self::assertStringContainsString('Placeholder mismatch', $errors[0]);
    }

    public function test_escaped_percent_is_not_a_placeholder(): void
    {
        $errors = $this->validator->validatePlaceholders(
            ['Progress: 100%% for %s' => 'Progress: 100%% for %s'],
            ['Progress: 100%% for %s' => 'Progresso: 100%% para %s'],
        );

        self::assertSame([], $errors);
    }

    public function test_missing_and_extra_translation_keys_are_not_policy_errors(): void
    {
        $errors = $this->validator->validatePlaceholders(
            ['Hello' => 'Hello', 'By %s' => 'By %s'],
            ['Unknown' => 'Desconhecido'],
        );

        self::assertSame([], $errors);
    }

    public function test_canonical_source_requires_identical_values(): void
    {
        self::assertSame([], $this->validator->validateCanonicalSource(['Hello' => 'Hello']));

        $errors = $this->validator->validateCanonicalSource(['Hello' => 'Olá']);
        self::assertCount(1, $errors);
    }
}
