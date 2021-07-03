<?php declare(strict_types = 1);

namespace Sloganator\Service;

class SloganList extends SloganResult {
    /**
     * @var Slogan[]
     */
    public array $slogans;

    /**
     * @var ListMeta
     */
    public ListMeta $meta;

    /**
     * @param array<Slogan> $slogans
     * @param ListMeta $meta
     */
    public function __construct(array $slogans, ListMeta $meta) {
        $this->slogans = $slogans;
        $this->meta = $meta;
    }
}