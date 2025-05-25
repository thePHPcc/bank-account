<?php declare(strict_types=1);
namespace example\caledonia\application;

use function assert;
use function file_exists;
use function file_get_contents;
use function is_string;

/**
 * @no-named-arguments
 */
final readonly class FilesystemMarketHtmlProjectionReader implements MarketHtmlProjectionReader
{
    /**
     * @var non-empty-string
     */
    private string $path;

    /**
     * @param non-empty-string $path
     */
    public function __construct(string $path)
    {
        assert(file_exists($path));

        $this->path = $path;
    }

    public function read(): string
    {
        $buffer = file_get_contents($this->path);

        assert(is_string($buffer));

        return $buffer;
    }
}
