<?php declare(strict_types = 1);

namespace Sloganator\Responses;

trait HttpResponse {
    /**
     * @var array<int, string> 
     */
    const array CODES = [
        200 => "OK",
        201 => "Created",
        204 => "No Content",
        400 => "Bad Request",
        401 => "Unauthorized",
        404 => "Not Found",
        405 => "Method Not Allowed",
        429 => "Too Many Requests",
        500 => "Internal Server Error"
    ];

    protected string $contentType;

    protected int $code;

    protected mixed $content;

    /**
     * @var string[]
     */
    protected array $extraHeaders = [];

    public function setCode(int $code): static {
        $this->code = $code;
        return $this;
    }

    public function setContent(mixed $content): static {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string[] $headers
     */
    public function addHeaders(array $headers): static {
        $this->extraHeaders = array_merge($this->extraHeaders, $headers);
        return $this;
    }

    public function setCacheHeaders(string $lastModified): static {
        $this->addHeaders([
            "X-Cache: HIT",
            "Last-Modified: " . $lastModified
        ]);

        return $this;
    }

    #[\Override]
    public function getContentType(): string {
        return $this->contentType;
    }
    
    #[\Override]
    public function getCodeString(): string {
        return sprintf("%d %s", $this->code, self::CODES[$this->code]);
    }
    
    #[\Override]
    public function respond(): void {
        header(sprintf("HTTP/1.1  %s", $this->getCodeString()));
        header(sprintf("Content-Type: %s;charset=%s", $this->getContentType(), Response::CHARSET_UTF8));

        foreach ($this->extraHeaders as $extraHeader) {
            header($extraHeader);
        }

        echo $this->getContent();
    }
}
