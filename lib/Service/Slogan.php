<?php declare(strict_types = 1);

namespace Sloganator\Service;

class Slogan {
    public int $rowid;
    public int $timestamp;
    public string $username;
    public int $userid;
    public string $slogan;

    /**
     * @param array<string, mixed> $row
     */
    public function __construct(array $row) {
        $this->rowid = $row["rowid"];
        $this->timestamp = $row["timestamp"];
        $this->username = $row["username"];
        $this->userid = $row["userid"];
        $this->slogan = $row["slogan"];
    }
}