<?php declare(strict_types = 1);

namespace Sloganator;

if (!defined("DOCROOT")) {
    define("DOCROOT", dirname(dirname(dirname(__FILE__))));
}

class User {
    const DEFAULT_USER_ID = 0;
    const DEFAULT_USER_NAME = "Treefort Lover";

    protected int $userId;
    protected string $userName;

    public function __construct() {
        $this->authenticate();
    }

    public function getUserName(): string {
        return $this->userName;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function isLoggedIn(): bool {
        return $this->userId > 0;
    }

    protected function authenticate(): void {
        global $db, $cache, $plugins;
        global $groupscache, $forum_cache, $fpermcache, $mybb;
        global $cached_forum_permissions_permissions, $cached_forum_permissions;
        global $grouppermignore, $groupzerogreater, $groupzerolesser, $groupxgreater, $grouppermbyswitch;

        // sigh... gotta keep MyBB in line
        ob_start();

        if (!defined("IN_MYBB")) {
            define("IN_MYBB", true);
        }

        require_once DOCROOT . "/inc/init.php";
        /* @phpstan-ignore-next-line */
        require_once MYBB_ROOT . "inc/class_session.php";

        try {
            if (isset($mybb)) {
                /* @phpstan-ignore-next-line */
                $session = new \session();
                /* @phpstan-ignore-next-line */
                $session->init();
                $mybb->session = &$session;
            }
        } catch (\Exception $e) {
            // mimic empty user data from mybb
            $mybb = new \StdClass;
            $mybb->user = [
                "username" => null, 
                "uid" => 0
            ];
        } finally {
            $this->userName = $mybb->user["username"] ?: self::DEFAULT_USER_NAME;
            $this->userId = (int) $mybb->user["uid"] ?: self::DEFAULT_USER_ID;
        }

        ob_end_clean();
    }
}

