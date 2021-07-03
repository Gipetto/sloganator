<?php declare(strict_types = 1);

namespace Sloganator\Service;

class Author {
    /**
     * @var int
     */
    public int $userid;

    /**
     * @var string[]
     */
    public array $usernames;

    /**
     * @param int $userid
     * @param string[] $usernames
     */
    public function __construct(int $userid, array $usernames = []) {
        $this->userid = $userid;
        $this->usernames = $usernames;
    }

    public function addUsername(string $username): void {
        array_push($this->usernames, $username);
    }
}