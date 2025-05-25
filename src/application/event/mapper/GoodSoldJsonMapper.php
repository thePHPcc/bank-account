<?php declare(strict_types=1);
namespace example\caledonia\application;

use function assert;
use example\caledonia\domain\Good;
use example\caledonia\domain\GoodSoldEvent;
use example\caledonia\domain\Price;
use example\framework\event\Event;
use example\framework\event\EventArrayMapper;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final class GoodSoldJsonMapper implements EventArrayMapper
{
    /**
     * @param array{event_id: non-empty-string, good: 'bread'|'cheese'|'grain'|'milk'|'whisky'|'wool', price: positive-int, amount: positive-int} $data
     *
     * @phpstan-ignore method.childParameterType
     */
    public function fromArray(array $data): GoodSoldEvent
    {
        return new GoodSoldEvent(
            Uuid::from($data['event_id']),
            Good::from($data['good']),
            Price::from($data['price']),
            $data['amount'],
        );
    }

    /**
     * @return array{good: 'bread'|'cheese'|'grain'|'milk'|'whisky'|'wool', price: positive-int, amount: positive-int}
     *
     * @phpstan-ignore method.childReturnType
     */
    public function toArray(Event $event): array
    {
        assert($event instanceof GoodSoldEvent);

        return [
            'good'   => $event->good()->value,
            'price'  => $event->price()->asInt(),
            'amount' => $event->amount(),
        ];
    }
}
