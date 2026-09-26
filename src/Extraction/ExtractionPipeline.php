<?php

namespace LibreSign\JigsawLocalization\Extraction;

use LibreSign\JigsawLocalization\Contracts\TranslationStringExtractor;
use InvalidArgumentException;

/**
 * Runs registered extractors only for the configured source locale.
 *
 * Filtering happens before extractors are invoked, preventing translated
 * sources from contaminating the canonical source catalog.
 */
final class ExtractionPipeline
{
    /** @var list<TranslationStringExtractor> */
    private array $extractors;

    private ExtractedStringNormalizer $normalizer;

    /**
     * @param  iterable<TranslationStringExtractor>  $extractors
     */
    public function __construct(
        private string $sourceLocale,
        iterable $extractors,
        ?ExtractedStringNormalizer $normalizer = null,
    ) {
        if ($sourceLocale === '') {
            throw new InvalidArgumentException('The source locale cannot be empty.');
        }

        $this->extractors = $this->collectExtractors($extractors);
        $this->normalizer = $normalizer ?? new ExtractedStringNormalizer;
    }

    /**
     * @param  iterable<TranslationSource>  $sources
     * @return list<ExtractedString>
     */
    public function extract(iterable $sources): array
    {
        /** @var array<string, ExtractedString> $strings */
        $strings = [];

        foreach ($sources as $source) {
            if ($source->locale() === $this->sourceLocale) {
                $this->collectSource($source, $strings);
            }
        }

        ksort($strings);

        return array_values($strings);
    }

    /**
     * @param  iterable<TranslationSource>  $sources
     * @return array<string, string>
     */
    public function catalog(iterable $sources): array
    {
        $catalog = [];

        foreach ($this->extract($sources) as $string) {
            $catalog[$string->text()] = $string->text();
        }

        return $catalog;
    }

    /**
     * @param  iterable<TranslationStringExtractor>  $extractors
     * @return list<TranslationStringExtractor>
     */
    private function collectExtractors(iterable $extractors): array
    {
        $collected = [];

        foreach ($extractors as $extractor) {
            $collected[] = $extractor;
        }

        return $collected;
    }

    /**
     * @param  array<string, ExtractedString>  $strings
     */
    private function collectSource(TranslationSource $source, array &$strings): void
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->supports($source)) {
                $this->collectExtractor($extractor, $source, $strings);
            }
        }
    }

    /**
     * @param  array<string, ExtractedString>  $strings
     */
    private function collectExtractor(
        TranslationStringExtractor $extractor,
        TranslationSource $source,
        array &$strings,
    ): void {
        foreach ($extractor->extract($source) as $extracted) {
            /** @var mixed $extracted */
            $normalized = $this->normalizer->normalize($extracted, $source, $extractor::class);
            $strings[$normalized->text()] ??= $normalized;
        }
    }
}
