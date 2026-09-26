<?php

namespace ElaborateCode\JigsawLocalization\Extraction;

use ElaborateCode\JigsawLocalization\Contracts\TranslationStringExtractor;
use InvalidArgumentException;
use UnexpectedValueException;

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

    /**
     * @param  iterable<TranslationStringExtractor>  $extractors
     */
    public function __construct(
        private string $sourceLocale,
        iterable $extractors,
    ) {
        if ($sourceLocale === '') {
            throw new InvalidArgumentException('The source locale cannot be empty.');
        }

        $this->extractors = [];
        foreach ($extractors as $extractor) {
            $this->extractors[] = $extractor;
        }
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
            if ($source->locale() !== $this->sourceLocale) {
                continue;
            }

            foreach ($this->extractors as $extractor) {
                if (! $extractor->supports($source)) {
                    continue;
                }

                foreach ($extractor->extract($source) as $extracted) {
                    if (is_string($extracted)) {
                        $extracted = new ExtractedString($extracted, $source->identifier());
                    }

                    if (! $extracted instanceof ExtractedString) {
                        throw new UnexpectedValueException(sprintf(
                            'Extractor %s must yield strings or %s instances.',
                            $extractor::class,
                            ExtractedString::class,
                        ));
                    }

                    $strings[$extracted->text()] ??= $extracted;
                }
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
}
