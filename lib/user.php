<?php declare(strict_types = 1);

if (!defined("DOCROOT")) {
    define("DOCROOT", dirname(dirname(dirname(__FILE__))));
}

class User {
    const DEFAULT_USER_ID = 0;
    const DEFAULT_USER_NAME = "Treefort Lover";

    private int $userId;
    private ?string $userName;

    public function __construct() {
        $this->authenticate();
    }

    public function getUserName(): ?string {
        return $this->userName;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function isLoggedIn(): bool {
        return $this->userId > 0;
    }

    private function authenticate(): void {
        global $db, $cache, $plugins;
        global $groupscache, $forum_cache, $fpermcache, $mybb;
        global $cached_forum_permissions_permissions, $cached_forum_permissions;

        if (!defined("IN_MYBB")) {
            define("IN_MYBB", true);
        }

        require_once DOCROOT . "/inc/init.php";
        require_once MYBB_ROOT . "inc/class_session.php";

        try {
            if (isset($mybb)) {
                $session = new \session();
                $session->init();
                $mybb->session = &$session;
            }
        } catch (\Exception $e) {
            // mimic empty user data from mybb
            $mybb = new StdClass;
            $mybb->user = [
                "username" => null, 
                "uid" => 0
            ];
        } finally {
            $this->userName = $mybb->user["username"] ?: self::DEFAULT_USER_NAME;
            $this->userId = (int) $mybb->user["uid"] ?: self::DEFAULT_USER_ID;
        }
    }
}

