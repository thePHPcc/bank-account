<?php declare(strict_types=1);
namespace example\framework\http;

use function header;

/**
 * @no-named-arguments
 */
final class Response
{
    /**
     * @var list<string>
     */
    private array $headers = [];
    private string $body   = '';

    public function addHeader(string $header): void
    {
        $this->headers[] = $header;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function send(): void
    {
        // @codeCoverageIgnoreStart
        foreach ($this->headers as $header) {
            header($header);
        }
        // @codeCoverageIgnoreEnd

        print $this->body;
    }

    public function body(): string
    {
        return $this->body;
    }

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return $this->headers;
    }
}
