<?php declare(strict_types = 1);

namespace Sloganator\Service;

class SloganList extends SloganResult {
    /**
     * @param array<Slogan> $slogans
     * @param ListMeta $meta
     */
    public function __construct(public array $slogans, public ListMeta $meta) {}
}