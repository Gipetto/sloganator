<?php declare(strict_types = 1);

namespace Sloganator\Cache;

use Carbon\Carbon;
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
    public function get(): mixed {
        /**
         * @var ApiResponse $response
         */
        $response = parent::get();

        if ($response instanceof ApiResponse) {
            $mtime = (int) filemtime($this->cacheFilePath());
            $response->setCacheHeaders((Carbon::now())->format("r"));
        }

        return $response;
    }
}
