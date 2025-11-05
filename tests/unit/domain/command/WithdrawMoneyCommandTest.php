<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WithdrawMoneyCommand::class)]
#[UsesClass(Money::class)]
#[UsesClass(MoneyFormatter::class)]
#[UsesClass(Currency::class)]
#[Small]
final class WithdrawMoneyCommandTest extends TestCase
{
    private const int AMOUNT           = 123;
    private const string CURRENCY_CODE = 'EUR';
    private const string DESCRIPTION   = 'the-description';

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame("Withdraw 1,23\u{a0}€", $this->command()->asString());
    }

    public function testHasAmount(): void
    {
        $this->assertSame(self::AMOUNT, $this->command()->amount()->amount());
        $this->assertSame(self::CURRENCY_CODE, $this->command()->amount()->currency()->currencyCode());
    }

    public function testHasDescription(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->command()->description());
    }

    private function command(): WithdrawMoneyCommand
    {
        return new WithdrawMoneyCommand(
            Money::from(self::AMOUNT, Currency::from(self::CURRENCY_CODE)),
            self::DESCRIPTION,
        );
    }
}
