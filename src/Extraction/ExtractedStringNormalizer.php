<?php

namespace ElaborateCode\JigsawLocalization\Extraction;

use UnexpectedValueException;

final class ExtractedStringNormalizer
{
    public function normalize(
        mixed $extracted,
        TranslationSource $source,
        string $extractorClass,
    ): ExtractedString {
        if ($extracted instanceof ExtractedString) {
            return $extracted;
        }

        if (is_string($extracted)) {
            return new ExtractedString($extracted, $source->identifier());
        }

        throw new UnexpectedValueException(sprintf(
            'Extractor %s must yield strings or %s instances.',
            $extractorClass,
            ExtractedString::class,
        ));
    }
}
