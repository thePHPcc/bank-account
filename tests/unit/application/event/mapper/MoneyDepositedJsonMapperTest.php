<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyDepositedJsonMapper::class)]
#[UsesClass(MoneyDepositedEvent::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class MoneyDepositedJsonMapperTest extends TestCase
{
    #[TestDox('Maps array to MoneyDepositedEvent')]
    public function testMapsArrayToMoneyDepositedEvent(): void
    {
        $eventId     = Uuid::from('4353726b-ca3a-4361-97eb-a0c54a5359f4');
        $amount      = 123;
        $currency    = 'EUR';
        $description = 'the-description';

        $event = (new MoneyDepositedJsonMapper)->fromArray(
            [
                'event_id'    => $eventId->asString(),
                'amount'      => $amount,
                'currency'    => $currency,
                'description' => $description,
            ],
        );

        $this->assertSame($eventId->asString(), $event->id()->asString());
        $this->assertSame($amount, $event->amount()->amount());
        $this->assertSame($currency, $event->amount()->currency()->currencyCode());
        $this->assertSame($description, $event->description());
    }

    #[TestDox('Maps MoneyDepositedEvent to array')]
    public function testMapsMoneyDepositedEventToArray(): void
    {
        $amount      = 123;
        $currency    = 'EUR';
        $description = 'the-description';

        $this->assertSame(
            [
                'amount'      => $amount,
                'currency'    => $currency,
                'description' => $description,
            ],
            (new MoneyDepositedJsonMapper)->toArray(
                new MoneyDepositedEvent(
                    Uuid::from('517e1ac8-c9d2-4955-9a59-7eb9ca519ede'),
                    Money::from($amount, Currency::from($currency)),
                    $description,
                ),
            ),
        );
    }
}
