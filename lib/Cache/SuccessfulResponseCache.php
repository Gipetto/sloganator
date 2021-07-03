<?php declare(strict_types = 1);

namespace Sloganator\Cache;

use Sloganator\Responses\ApiResponse;

class SuccessfulResponseCache extends SerializingFileCache {
    /**
     * @param ApiResponse $response
     */
    public function set($response): int {
        if (!($response instanceof ApiResponse)) {
            throw new \InvalidArgumentException;
        }

        $r = clone $response;
        $r->setCode(200);
        return parent::set($r);
    }

    /**
     * @return ApiResponse
     */
    public function get() {
        /**
         * @var \ApiResponse $response
         */
        $response = parent::get();

        if ($response instanceof ApiResponse) {
            $mtime = (int) filemtime($this->cacheFilePath());
            $response->addHeaders([
                "X-Cache: HIT",
                "Last-Modified: " . date("r", $mtime)
            ]);
        }

        return $response;
    }
}
