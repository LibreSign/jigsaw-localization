<?php

namespace LibreSign\JigsawLocalization\Contracts;

use LibreSign\JigsawLocalization\Extraction\ExtractedString;
use LibreSign\JigsawLocalization\Extraction\TranslationSource;

interface TranslationStringExtractor
{
    public function supports(TranslationSource $source): bool;

    /**
     * @return iterable<ExtractedString|string>
     */
    public function extract(TranslationSource $source): iterable;
}
