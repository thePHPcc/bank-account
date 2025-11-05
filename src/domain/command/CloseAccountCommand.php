<?php declare(strict_types=1);
namespace example\bankaccount\domain;

/**
 * @no-named-arguments
 */
final readonly class CloseAccountCommand extends Command
{
    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return 'Close account';
    }
}
