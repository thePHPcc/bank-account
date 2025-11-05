<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyWithdrawnJsonMapper::class)]
#[UsesClass(MoneyWithdrawnEvent::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class MoneyWithdrawnJsonMapperTest extends TestCase
{
    #[TestDox('Maps array to MoneyWithdrawnEvent')]
    public function testMapsArrayToMoneyWithdrawnEvent(): void
    {
        $eventId     = Uuid::from('6051a259-eb72-4c5e-bdf5-8f32e129f93a');
        $amount      = 123;
        $currency    = 'EUR';
        $description = 'the-description';

        $event = (new MoneyWithdrawnJsonMapper)->fromArray(
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

    #[TestDox('Maps MoneyWithdrawnEvent to array')]
    public function testMapsMoneyWithdrawnEventToArray(): void
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
            (new MoneyWithdrawnJsonMapper)->toArray(
                new MoneyWithdrawnEvent(
                    Uuid::from('da820135-3991-4b69-a55a-27c0ce453fa3'),
                    Money::from($amount, Currency::from($currency)),
                    $description,
                ),
            ),
        );
    }
}
