<?php declare(strict_types = 1);

namespace Sloganator\Service;

class Slogan {
    public function __construct(
        public int $rowid,
        public int $timestamp,
        public string $username,
        public int $userid,
        public string $slogan
    ) {}
}