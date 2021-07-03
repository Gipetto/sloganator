<?php declare(strict_types = 1);

interface Response {
    const CONTENT_TYPE_JSON = "application/json";
    const CONTENT_TYPE_HTML = "text/html";

    function getContentType(): string;
    function getCodeString(): string;
    function getContent(): string;
    function respond(): void;
}

trait HttpResponse {
    /**
     * @var array<int, string> 
     */
    private $codes = [
        200 => "OK",
        201 => "Created",
        400 => "Bad Request",
        401 => "Unauthorized",
        404 => "Not Found",
        429 => "Too Many Requests"
    ];

    protected string $contentType;

    protected int $code;

    /**
     * @var string[]
     */
    protected array $extraHeaders = [];

    public function setCode(int $code): int {
        return $this->code = $code;
    }

    /**
     * @param string[] $headers
     */
    public function addHeaders(array $headers): void {
        $this->extraHeaders = array_merge($this->extraHeaders, $headers);
    }

    public function getContentType(): string {
        return $this->contentType;
    }

    public function getCodeString(): string {
        return sprintf("%d %s", $this->code, $this->codes[$this->code]);
    }

    public function respond(): void {
        header(sprintf("HTTP/1.0  %s", $this->getCodeString()));
        header(sprintf("Content-Type: %s", $this->getContentType()));

        foreach ($this->extraHeaders as $extraHeader) {
            header($extraHeader);
        }

        echo $this->getContent();
        exit;
    }
}

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

class Unauthorized extends ApiResponse {
    public function __construct() {
        parent::__construct(401, (object) [
            "code" => 401,
            "message" => "You must be logged in to create a slogan"
        ]);
    }
}

class ValidationError extends ApiResponse {
    public function __construct(string $message) {
        parent::__construct(400, (object) [
            "code" => 400,
            "message" => $message
        ]);
    }
}

class NotFound extends ApiResponse {
    public function __construct() {
        parent::__construct(404, (object) [
            "code" => 404,
            "message" => "Invalid Route"
        ]);
    }
}

class TooManyRequests extends ApiResponse {
    public function __construct(int $retryTime) {
        parent::__construct(429, (object) [
            "code" => 429,
            "message" => "Hang loose. Slow and steady wins the race."
        ]);
        $this->addHeaders([
            sprintf("Retry-After: %d", $retryTime)
        ]);
    }
}

class PageResponse implements Response {
    use HttpResponse;

    /**
     * @var array<string, mixed> $params
     */
    private array $params;
    private string $template;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct(int $code, string $template = "", array $params = []) {
        $this->setCode($code);
        $this->params = $params;
        $this->template = $template;
        $this->contentType = Response::CONTENT_TYPE_HTML;
    }

    public function getContent(): string {
        ob_start();
        extract($this->params);
        include "templates/" . $this->template . ".php";
        $output = ob_get_clean();

        return (string) $output;
    }
}

