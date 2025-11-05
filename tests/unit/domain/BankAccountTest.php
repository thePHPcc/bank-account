<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
final class BankAccountTest extends TestCase
{
    private const string OWNER         = 'the-owner';
    private const int AMOUNT           = 123;
    private const string CURRENCY_CODE = 'EUR';

    public function testHasOwner(): void
    {
        $this->assertSame(self::OWNER, $this->bankAccount()->owner());
    }

    public function testHasBalance(): void
    {
        $this->assertEquals($this->amount(), $this->bankAccount()->balance());
    }

    public function testIsInitiallyActive(): void
    {
        $this->assertTrue($this->bankAccount()->isActive());
    }

    public function testPositiveAmountOfMatchingCurrencyCanBeDeposited(): void
    {
        $bankAccount = $this->bankAccount();

        $bankAccount->deposit(Money::from(123, Currency::from('EUR')));

        $this->assertSame(246, $bankAccount->balance()->amount());
    }

    public function testPositiveAmountOfMatchingCurrencyCanBeWithdrawn(): void
    {
        $bankAccount = $this->bankAccount();

        $bankAccount->withdraw(Money::from(123, Currency::from('EUR')));

        $this->assertSame(0, $bankAccount->balance()->amount());
    }

    public function testCanBeClosed(): void
    {
        $bankAccount = $this->bankAccount();

        $bankAccount->close();

        $this->assertFalse($bankAccount->isActive());
    }

    public function testCannotBeClosedTwice(): void
    {
        $bankAccount = $this->bankAccount();

        $bankAccount->close();

        $this->expectException(AccountIsClosedException::class);

        $bankAccount->close();
    }

    public function testPositiveAmountOfNotMatchingCurrencyCannotBeDeposited(): void
    {
        $bankAccount = $this->bankAccount();

        $this->expectException(CurrencyMismatchException::class);

        $bankAccount->deposit(Money::from(123, Currency::from('GBP')));
    }

    public function testAmountThatIsNotPositiveCannotBeDeposited(): void
    {
        $bankAccount = $this->bankAccount();

        $this->expectException(AmountMustBePositiveException::class);

        $bankAccount->deposit(Money::from(0, Currency::from('EUR')));
    }

    public function testPositiveAmountOfNotMatchingCurrencyCannotBeWithdrawn(): void
    {
        $bankAccount = $this->bankAccount();

        $this->expectException(CurrencyMismatchException::class);

        $bankAccount->withdraw(Money::from(123, Currency::from('GBP')));
    }

    public function testAmountThatIsNotPositiveCannotBeWithdrawn(): void
    {
        $bankAccount = $this->bankAccount();

        $this->expectException(AmountMustBePositiveException::class);

        $bankAccount->withdraw(Money::from(0, Currency::from('EUR')));
    }

    private function bankAccount(): BankAccount
    {
        return BankAccount::from(
            self::OWNER,
            $this->amount(),
            true,
        );
    }

    private function amount(): Money
    {
        return Money::from(self::AMOUNT, Currency::from(self::CURRENCY_CODE));
    }
}
