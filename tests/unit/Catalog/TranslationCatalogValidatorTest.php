<?php

namespace Tests\Unit\Catalog;

use LibreSign\JigsawLocalization\Catalog\TranslationCatalogValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TranslationCatalogValidatorTest extends TestCase
{
    private TranslationCatalogValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new TranslationCatalogValidator;
    }

    #[DataProvider('validPlaceholderTranslations')]
    public function test_valid_placeholder_translations_are_accepted(string $source, string $translation): void
    {
        $errors = $this->validator->validatePlaceholders(
            [$source => $source],
            [$source => $translation],
        );

        self::assertSame([], $errors);
    }

    public static function validPlaceholderTranslations(): array
    {
        return [
            'single string' => ['By %s', 'Por %s'],
            'reordered positional placeholders' => [
                '%s signed %d documents',
                '%2$d documentos assinados por %1$s',
            ],
            'integer width' => ['Item %02d', 'Item %02d'],
            'float precision' => ['Total %.2f', 'Total %.2f'],
            'escaped percent' => ['Progress: 100%% for %s', 'Progresso: 100%% para %s'],
        ];
    }

    #[DataProvider('invalidPlaceholderTranslations')]
    public function test_placeholder_mismatches_are_reported(string $source, string $translation): void
    {
        $errors = $this->validator->validatePlaceholders(
            [$source => $source],
            [$source => $translation],
        );

        self::assertCount(1, $errors);
        self::assertStringContainsString('Placeholder mismatch', $errors[0]);
    }

    public static function invalidPlaceholderTranslations(): array
    {
        return [
            'missing placeholder' => ['By %s', 'Por'],
            'wrong placeholder type' => ['Count: %d', 'Contagem: %s'],
            'extra placeholder' => ['Hello', 'Olá %s'],
            'missing one of multiple placeholders' => ['%s has %d files', '%s tem arquivos'],
        ];
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
