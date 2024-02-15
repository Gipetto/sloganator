<?php declare(strict_types = 1);

namespace Sloganator\Service;

class Author {
    /**
     * @param int $userid
     * @param string[] $usernames
     */
    public function __construct(public int $userid, public array $usernames = []) {}

    public function addUsername(string $username): void {
        array_push($this->usernames, $username);
    }
}