<?php declare(strict_types = 1);

namespace Sloganator\Service;

class ListMeta {
    /**
     * @param array<string, int|string> $filter
     */
    public function __construct(
        public int $page,
        public int $pageSize,
        public int $results,
        public ?int $previousPage,
        public ?int $nextPage,
        public ?array $filter
    ) {}
}