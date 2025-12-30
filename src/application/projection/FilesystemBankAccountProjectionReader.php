<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use function file_exists;
use function file_get_contents;
use function is_string;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class FilesystemBankAccountProjectionReader implements BankAccountProjectionReader
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

    public function read(Uuid $accountId): string
    {
        $buffer = file_get_contents($this->path);

        assert(is_string($buffer));

        return $buffer;
    }
}
