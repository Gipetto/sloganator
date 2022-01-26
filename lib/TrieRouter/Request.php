<?php declare(strict_types = 1);

namespace Sloganator\TrieRouter;

final class Request {
    /**
     * @param array<string, mixed> $params
     * @param array<string, string> $headers
     */
    public function __construct(
        public string $method,
        public string $path,
        public array $params = [],
        public array $headers = []
    ) {}

    /**
     * Imperfect, but it means we don't have to dance around the 
     * php-cli not having this available during tests
     * 
     * @return array<string, string>
     */
    public static function getHttpHeaders(): array {
        $httpHeaders = array_filter($_SERVER, fn (string $key) => substr($key, 0, 5)=='HTTP_', ARRAY_FILTER_USE_KEY);

        $headers = [];
        foreach ($httpHeaders as $key => $value) {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value; 
        }

        return $headers;
    }

    public static function new(string $inputStream = "php://input"): static {
        $method = strtoupper($_SERVER["REQUEST_METHOD"]);
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) ?: "";
        $query = parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY) ?: "";
        parse_str($query, $params);

        if ($method == "POST") {
            $body = (string) file_get_contents($inputStream);
            $params["body"] = json_decode($body, true);
        }

        $headers = self::getHttpHeaders();

        return new static($method, $path, $params, $headers);
    }
}
