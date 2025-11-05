<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\framework\event\Event;
use example\framework\event\EventArrayMapper;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final class MoneyDepositedJsonMapper implements EventArrayMapper
{
    /**
     * @param array{event_id: non-empty-string, amount: int, currency: non-empty-string, description: non-empty-string} $data
     *
     * @phpstan-ignore method.childParameterType
     */
    public function fromArray(array $data): MoneyDepositedEvent
    {
        return new MoneyDepositedEvent(
            Uuid::from($data['event_id']),
            Money::from($data['amount'], Currency::from($data['currency'])),
            $data['description'],
        );
    }

    /**
     * @return array{amount: int, currency: non-empty-string, description: non-empty-string}
     *
     * @phpstan-ignore method.childReturnType
     */
    public function toArray(Event $event): array
    {
        assert($event instanceof MoneyDepositedEvent);

        return [
            'amount'      => $event->amount()->amount(),
            'currency'    => $event->amount()->currency()->currencyCode(),
            'description' => $event->description(),
        ];
    }
}
