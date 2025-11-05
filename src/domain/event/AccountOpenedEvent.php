<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\event\Event;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class AccountOpenedEvent extends Event
{
    /**
     * @var non-empty-string
     */
    private string $owner;

    /**
     * @param non-empty-string $owner
     */
    public function __construct(Uuid $id, string $owner)
    {
        parent::__construct($id);

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
    public function topic(): string
    {
        return 'banking.account-opened';
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return 'Account opened';
    }
}
