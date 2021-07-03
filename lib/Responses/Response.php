<?php declare(strict_types = 1);

namespace Sloganator\Responses;

interface Response {
    const CONTENT_TYPE_JSON = "application/json";
    const CONTENT_TYPE_HTML = "text/html";

    function getContentType(): string;
    function getCodeString(): string;
    function getContent(): string;
    function respond(): void;
}