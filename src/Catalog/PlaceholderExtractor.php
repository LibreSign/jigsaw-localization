<?php

namespace ElaborateCode\JigsawLocalization\Catalog;

/**
 * Extracts printf/vsprintf placeholders as stable argument-position/type tokens.
 */
final class PlaceholderExtractor
{
    /**
     * @return list<string>
     */
    public function extract(string $text): array
    {
        $text = str_replace('%%', '', $text);

        preg_match_all(
            "/%(?!%)(?:(?<position>\\d+)\\$)?[-+0' #]*(?:\\d+)?(?:\.\\d+)?(?<type>[bcdeEfFgGosuxX])/",
            $text,
            $matches,
            PREG_SET_ORDER,
        );

        $placeholders = $this->tokens($matches);
        sort($placeholders);

        return $placeholders;
    }

    /**
     * @param  array<int, array<string|int, string>>  $matches
     * @return list<string>
     */
    private function tokens(array $matches): array
    {
        $implicitPosition = 1;
        $tokens = [];

        foreach ($matches as $match) {
            $position = $match['position'] !== '' ? (int) $match['position'] : $implicitPosition++;
            $tokens[] = $position.':'.$match['type'];
        }

        return $tokens;
    }
}
