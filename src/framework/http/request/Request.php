<?php declare(strict_types=1);
namespace example\framework\http;

use function assert;
use function file_get_contents;
use function is_string;

/**
 * @no-named-arguments
 */
abstract readonly class Request
{
    private string $uri;

    /**
     * @throws UnsupportedRequestException
     */
    public static function fromSuperGlobals(): GetRequest|PostRequest
    {
        assert(isset($_SERVER['REQUEST_METHOD']));
        assert(is_string($_SERVER['REQUEST_METHOD']));
        assert(isset($_SERVER['REQUEST_URI']));
        assert(is_string($_SERVER['REQUEST_URI']));

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return new GetRequest(
                $_SERVER['REQUEST_URI'],
                /** @phpstan-ignore argument.type */
                $_GET,
            );
        }

        // @codeCoverageIgnoreStart
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = file_get_contents('php://input');

            assert(is_string($body));

            return new PostRequest($_SERVER['REQUEST_URI'], $body);
        }

        throw new UnsupportedRequestException;
        // @codeCoverageIgnoreEnd
    }

    protected function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * @phpstan-assert-if-true GetRequest $this
     */
    public function isGetRequest(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true PostRequest $this
     */
    public function isPostRequest(): bool
    {
        return false;
    }
}
