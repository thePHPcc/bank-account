<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\event\Event;

/**
 * @no-named-arguments
 */
final readonly class AccountClosedEvent extends Event
{
    /**
     * @return non-empty-string
     */
    public function topic(): string
    {
        return 'banking.account-closed';
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return 'Account closed';
    }
}
