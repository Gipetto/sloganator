<?php declare(strict_types = 1);

namespace Sloganator\Router;

final class Request {
    const string GET = "GET";
    const string POST = "POST";
    const string PUT = "PUT";
    const string DELETE = "DELETE";
    const string OPTIONS = "OPTIONS";

    const array METHODS = [
        self::GET,
        self::POST,
        self::PUT,
        self::DELETE,
        self::OPTIONS
    ];

    const array INPUT_BODY_METHODS = [
        self::POST,
        self::PUT
    ];

    /**
     * @param array<string, mixed> $params
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     */
    public function __construct(
        readonly public string $method,
        readonly public string $path,
        public array $params = [],
        readonly public ?array $body = null,
        readonly public array $headers = [],
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
        $body = null;

        $headers = self::getHttpHeaders();

        if (in_array($method, self::INPUT_BODY_METHODS)) {
            $_body = (string) file_get_contents($inputStream);
            
            // we currently only support JSON bodies, 'cause that's all we're using
            if (json_validate($_body)) {
                $body = json_decode($_body, true);
            }
        }

        // @phpstan-ignore argument.type
        return new static($method, $path, $params, $body, $headers);
    }    
}
