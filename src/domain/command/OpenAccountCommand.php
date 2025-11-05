<?php declare(strict_types=1);
namespace example\bankaccount\domain;

/**
 * @no-named-arguments
 */
final readonly class OpenAccountCommand extends Command
{
    /**
     * @var non-empty-string
     */
    private string $owner;

    /**
     * @param non-empty-string $owner
     */
    public function __construct(string $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return non-empty-string
     */
    public function owner(): string
    {
        return $this->owner;
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return 'Open account';
    }
}
