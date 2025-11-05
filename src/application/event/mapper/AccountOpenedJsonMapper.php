<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use example\bankaccount\domain\AccountOpenedEvent;
use example\framework\event\Event;
use example\framework\event\EventArrayMapper;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final class AccountOpenedJsonMapper implements EventArrayMapper
{
    /**
     * @param array{event_id: non-empty-string, owner: non-empty-string} $data
     *
     * @phpstan-ignore method.childParameterType
     */
    public function fromArray(array $data): AccountOpenedEvent
    {
        return new AccountOpenedEvent(
            Uuid::from($data['event_id']),
            $data['owner'],
        );
    }

    /**
     * @return array{owner: non-empty-string}
     */
    public function toArray(Event $event): array
    {
        assert($event instanceof AccountOpenedEvent);

        return [
            'owner' => $event->owner(),
        ];
    }
}
