<?php declare(strict_types=1);
namespace example\framework\event;

use function assert;
use example\framework\library\Uuid;

final readonly class DummyEventMapper implements EventArrayMapper
{
    /**
     * @param array{event_id: non-empty-string, correlation_id: non-empty-string, key: non-empty-string} $data
     *
     * @phpstan-ignore method.childParameterType
     */
    public function fromArray(array $data): DummyEvent
    {
        return new DummyEvent(
            Uuid::from($data['event_id']),
            Uuid::from($data['correlation_id']),
            $data['key'],
        );
    }

    /**
     * @return array{event_id: non-empty-string, correlation_id: non-empty-string, key: non-empty-string}
     */
    public function toArray(Event $event): array
    {
        assert($event instanceof DummyEvent);

        return [
            'event_id'       => $event->id()->asString(),
            'correlation_id' => $event->correlationId()->asString(),
            'key'            => $event->key(),
        ];
    }
}
