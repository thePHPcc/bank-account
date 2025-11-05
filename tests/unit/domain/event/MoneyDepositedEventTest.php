<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyDepositedEvent::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Money::class)]
#[UsesClass(MoneyFormatter::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class MoneyDepositedEventTest extends TestCase
{
    private const string UUID          = '0af488c7-ff59-4cd8-bbfe-6575deed88b1';
    private const int AMOUNT           = 123;
    private const string CURRENCY_CODE = 'EUR';
    private const string DESCRIPTION   = 'the-description';

    public function testHasId(): void
    {
        $this->assertSame(self::UUID, $this->event()->id()->asString());
    }

    public function testHasTopic(): void
    {
        $this->assertSame('banking.money-deposited', $this->event()->topic());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame("1,23\u{a0}€ deposited", $this->event()->asString());
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

    private function event(): MoneyDepositedEvent
    {
        return new MoneyDepositedEvent(
            new Uuid(self::UUID),
            Money::from(self::AMOUNT, Currency::from(self::CURRENCY_CODE)),
            self::DESCRIPTION,
        );
    }
}
