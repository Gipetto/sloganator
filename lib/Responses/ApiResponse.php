<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class ApiResponse implements Response {
    use HttpResponse;

    /**
     * @var object $content
     */
    private object $content;

    /**
     * @param int $code
     * @param object $content
     */
    public function __construct(int $code, object $content) {
        $this->setCode($code);
        $this->content = $content;
        $this->contentType = Response::CONTENT_TYPE_JSON;
        $this->addHeaders([
            "Cache-Control: no-cache"
        ]);
    }

    public function getContent(): string {
        return json_encode($this->content, JSON_THROW_ON_ERROR);
    }
}
