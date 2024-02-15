<?php declare(strict_types = 1);

namespace Sloganator\Responses;

class PageResponse implements Response {
    use HttpResponse;

    /**
     * @var array<string, mixed> $params
     */
    private array $params;
    private string $templatesDir = "templates/";
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

    public function setTemplatesDir(string $dir): void {
        $this->templatesDir = $dir;
    }

    public function getContent(): string {
        ob_start();
        extract($this->params);
        include $this->templatesDir . $this->template . ".php";
        $output = ob_get_clean();

        return (string) $output;
    }
}
