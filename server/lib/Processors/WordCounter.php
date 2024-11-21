<?php declare(strict_types = 1);

namespace Sloganator\Processors;

class WordCounter {
    const NUM_RETURNABLE_ENTITIES = 100;

    /**
     * @var string[]
     */
    protected array $stopWords;

    public function __construct(protected \Closure $generator) {
        $this->stopWords = $this->loadStopWords();
    }

    /**
     * @return string[]
     */
    public function loadStopWords(): array {
        $filePath = dirname(__FILE__) . "/stop-words.csv";
        /**
         * @var string
         */
        $text = file_get_contents($filePath);
        return explode("\n", trim($text));
    }

    public function fetchData(): \Closure {
        return $this->generator;
    }

    /**
     * @return object[]
     */
    public function run(int $numEntities = self::NUM_RETURNABLE_ENTITIES): array {
        $words = [];

        try {
            $iterator = $this->fetchData();
            foreach ($iterator() as $string) {
                $tokens = $this->parseTokens($string);
                foreach ($tokens as $token) {
                    if (!isset($words[$token])) {
                        $words[$token] = (object) [
                            "x" => $token,
                            "value" => 0
                        ];
                    }
                    $words[$token]->value++;
                }
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }

        // order by count, then alpha by word
        uasort($words, fn($a, $b) => ([$a->value, $a->x] <=> [$b->value, $b->x]) * -1);

        $topWords = array_slice($words, 0, $numEntities);
        return array_values($topWords);
    }

    /**
     * @return string[]
     */
    public function parseTokens(string $slogan): array {
        /**
         * @var string[]|false $tokens
         */
        // @phpstan-ignore varTag.nativeType
        $tokens = preg_split("/\s/i", $slogan, -1, PREG_SPLIT_NO_EMPTY);

        if (!$tokens) {
            return [];
        }

        $strippedTokens = array_map(function($word) {
            return preg_replace([
                "/^([[:punct:]]+)/", // punctuation from beginning of string
                "/([[:punct:]]+|'s)$/" // punctuation and possessives from end of string
            ], "", $word) ?: "";
        }, $tokens);

        $lowerTokens = array_map(fn($word) => strtolower($word), $strippedTokens);

        $prunedTokens = array_filter($lowerTokens, function($word) {
            $strippedWord = preg_replace("/[[:punct:]\s,]+/", "", $word);
            return !(
                empty($strippedWord) || 
                strlen($word) == 1 || 
                is_numeric($word) || 
                in_array($word, $this->stopWords)
            );
        });

        return $prunedTokens;
    }
}
