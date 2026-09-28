<?php

namespace Tests\Unit\Extraction;

use LibreSign\JigsawLocalization\Extraction\CallbackStringExtractor;
use LibreSign\JigsawLocalization\Extraction\ExtractedString;
use LibreSign\JigsawLocalization\Extraction\ExtractionPipeline;
use LibreSign\JigsawLocalization\Extraction\TranslationSource;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class ExtractionPipelineTest extends TestCase
{
    public function test_it_never_invokes_extractors_for_non_source_locales(): void
    {
        $calls = 0;
        $extractor = new CallbackStringExtractor(
            static fn (): bool => true,
            static function (TranslationSource $source) use (&$calls): iterable {
                $calls++;

                yield $source->contents();
            },
        );

        $pipeline = new ExtractionPipeline('en', [$extractor]);

        $catalog = $pipeline->catalog([
            new TranslationSource('pt-BR', 'pt/home.blade.php', 'Olá'),
            new TranslationSource('en', 'home.blade.php', 'Hello'),
        ]);

        self::assertSame(1, $calls);
        self::assertSame(['Hello' => 'Hello'], $catalog);
    }

    public function test_it_runs_only_extractors_that_support_a_source(): void
    {
        $extractor = new CallbackStringExtractor(
            static fn (TranslationSource $source): bool => str_ends_with($source->identifier(), '.md'),
            static fn (TranslationSource $source): array => [$source->contents()],
        );

        $pipeline = new ExtractionPipeline('en', [$extractor]);

        self::assertSame(
            ['Markdown' => 'Markdown'],
            $pipeline->catalog([
                new TranslationSource('en', 'page.blade.php', 'Blade'),
                new TranslationSource('en', 'post.md', 'Markdown'),
            ]),
        );
    }

    public function test_it_deduplicates_strings_and_keeps_first_reference(): void
    {
        $extractor = new CallbackStringExtractor(
            static fn (): bool => true,
            static fn (TranslationSource $source): array => [
                new ExtractedString('Same', $source->identifier(), 7, 'title'),
            ],
        );

        $pipeline = new ExtractionPipeline('en', [$extractor]);
        $strings = $pipeline->extract([
            new TranslationSource('en', 'first.md', 'ignored'),
            new TranslationSource('en', 'second.md', 'ignored'),
        ]);

        self::assertCount(1, $strings);
        self::assertSame('Same', $strings[0]->text());
        self::assertSame('first.md', $strings[0]->source());
        self::assertSame(7, $strings[0]->line());
        self::assertSame('title', $strings[0]->context());
    }

    public function test_string_results_receive_the_source_identifier(): void
    {
        $extractor = new CallbackStringExtractor(
            static fn (): bool => true,
            static fn (): array => ['Hello'],
        );

        $pipeline = new ExtractionPipeline('en', [$extractor]);
        $strings = $pipeline->extract([
            new TranslationSource('en', 'home.blade.php', 'ignored'),
        ]);

        self::assertSame('home.blade.php', $strings[0]->source());
    }

    public function test_invalid_extractor_result_is_rejected(): void
    {
        $extractor = new CallbackStringExtractor(
            static fn (): bool => true,
            static fn (): array => [123],
        );

        $this->expectException(UnexpectedValueException::class);

        (new ExtractionPipeline('en', [$extractor]))->extract([
            new TranslationSource('en', 'home.blade.php', 'ignored'),
        ]);
    }
}
