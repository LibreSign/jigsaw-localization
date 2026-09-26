<?php

namespace ElaborateCode\JigsawLocalization\Extraction;

use Closure;
use ElaborateCode\JigsawLocalization\Contracts\TranslationStringExtractor;

/**
 * Adapter for projects that want to provide extraction logic without creating
 * a dedicated extractor class.
 */
final class CallbackStringExtractor implements TranslationStringExtractor
{
    /**
     * @param  callable(TranslationSource): bool  $supports
     * @param  callable(TranslationSource): iterable<ExtractedString|string>  $extract
     */
    public function __construct(
        callable $supports,
        callable $extract,
    ) {
        $this->supports = Closure::fromCallable($supports);
        $this->extract = Closure::fromCallable($extract);
    }

    private Closure $supports;

    private Closure $extract;

    public function supports(TranslationSource $source): bool
    {
        return ($this->supports)($source);
    }

    public function extract(TranslationSource $source): iterable
    {
        return ($this->extract)($source);
    }
}
