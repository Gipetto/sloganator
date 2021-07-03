<?php declare(strict_types = 1);

namespace Sloganator\Responses;

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
