<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class ApiResponse implements Response {
    use HttpResponse;

    /**
     * @param int $code
     * @param object $content
     */
    public function __construct(int $code, object $content) {
        $this->setCode($code);
        $this->setContent($content);
        $this->contentType = Response::CONTENT_TYPE_JSON;
        $this->addHeaders([
            "Access-Control-Allow-Origin: *",
            "Access-Control-Allow-Methods: GET, POST, PUT, DELTE",
            "Access-Control-Allow-Headers: Content-Type",
            "Cache-Control: no-cache"
        ]);
    }

    public function getContent(): string {
        return json_encode($this->content, JSON_THROW_ON_ERROR);
    }
}
