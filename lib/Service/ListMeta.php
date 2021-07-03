<?php declare(strict_types = 1);

namespace Sloganator\Service;

class ListMeta {
    public int $page;
    public int $pageSize;
    public int $results;
    public ?int $previousPage;
    public ?int $nextPage;

    /**
     * @var array<string, string|int>
     */
    public ?array $filter;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(array $params) {
        $this->page = $params["page"];
        $this->pageSize = $params["pageSize"];
        $this->results = $params["results"];
        $this->previousPage = $params["previousPage"];
        $this->nextPage = $params["nextPage"];
        $this->filter = $params["filter"];
    }
}