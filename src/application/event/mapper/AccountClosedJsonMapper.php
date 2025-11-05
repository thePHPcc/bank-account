<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use example\bankaccount\domain\AccountClosedEvent;
use example\framework\event\Event;
use example\framework\event\EventArrayMapper;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final class AccountClosedJsonMapper implements EventArrayMapper
{
    /**
     * @param array{event_id: non-empty-string} $data
     *
     * @phpstan-ignore method.childParameterType
     */
    public function fromArray(array $data): AccountClosedEvent
    {
        return new AccountClosedEvent(
            Uuid::from($data['event_id']),
        );
    }

    public function toArray(Event $event): array
    {
        assert($event instanceof AccountClosedEvent);

        return [];
    }
}
