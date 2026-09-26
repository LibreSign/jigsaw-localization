<?php

namespace ElaborateCode\JigsawLocalization\Contracts;

use ElaborateCode\JigsawLocalization\Extraction\ExtractedString;
use ElaborateCode\JigsawLocalization\Extraction\TranslationSource;

interface TranslationStringExtractor
{
    public function supports(TranslationSource $source): bool;

    /**
     * @return iterable<ExtractedString|string>
     */
    public function extract(TranslationSource $source): iterable;
}
