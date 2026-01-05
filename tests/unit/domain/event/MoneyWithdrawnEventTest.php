<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyWithdrawnEvent::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Money::class)]
#[UsesClass(MoneyFormatter::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/domain')]
#[Group('unit/domain/event')]
#[Small]
final class MoneyWithdrawnEventTest extends TestCase
{
    private const string ID             = '5d1ae682-c6c2-4dbd-b88f-541660be2508';
    private const string CORRELATION_ID = 'cbbce7f1-6e3a-40ed-8250-a10fd1069298';
    private const int AMOUNT            = 123;
    private const string CURRENCY_CODE  = 'EUR';
    private const string DESCRIPTION    = 'the-description';

    public function testHasId(): void
    {
        $this->assertSame(self::ID, $this->event()->id()->asString());
    }

    public function testHasCorrelationId(): void
    {
        $this->assertSame(self::CORRELATION_ID, $this->event()->correlationId()->asString());
    }

    public function testHasTopic(): void
    {
        $this->assertSame('banking.money-withdrawn', $this->event()->topic());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame("1,23\u{a0}€ withdrawn", $this->event()->asString());
    }

    public function testHasAmount(): void
    {
        $this->assertSame(self::AMOUNT, $this->event()->amount()->amount());
        $this->assertSame(self::CURRENCY_CODE, $this->event()->amount()->currency()->currencyCode());
    }

    public function testHasDescription(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->event()->description());
    }

    public function testAmountMustBePositive(): void
    {
        $this->expectException(AmountMustBePositiveException::class);

        new MoneyWithdrawnEvent(
            new Uuid(self::ID),
            new Uuid(self::CORRELATION_ID),
            Money::from(0, Currency::from(self::CURRENCY_CODE)),
            self::DESCRIPTION,
        );
    }

    private function event(): MoneyWithdrawnEvent
    {
        return new MoneyWithdrawnEvent(
            new Uuid(self::ID),
            new Uuid(self::CORRELATION_ID),
            Money::from(self::AMOUNT, Currency::from(self::CURRENCY_CODE)),
            self::DESCRIPTION,
        );
    }
}
