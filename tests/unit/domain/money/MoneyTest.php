<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
final class MoneyTest extends TestCase
{
    public function testHasAmount(): void
    {
        $amount = 123;
        $money  = Money::from($amount, Currency::from('EUR'));

        $this->assertSame($amount, $money->amount());
    }

    public function testAmountCanBeConvertedToFloat(): void
    {
        $money = Money::from(123, Currency::from('EUR'));

        $this->assertSame(1.23, $money->convertedAmount());
    }

    public function testHasCurrency(): void
    {
        $currency = Currency::from('EUR');
        $money    = Money::from(123, $currency);

        $this->assertSame($currency, $money->currency());
    }

    public function testCanBeNegated(): void
    {
        $money = Money::from(123, Currency::from('EUR'));

        $this->assertSame(-123, $money->negate()->amount());
    }

    public function testAnotherMoneyObjectWithSameCurrencyCanBeAdded(): void
    {
        $currency = Currency::from('EUR');
        $a        = Money::from(1, $currency);
        $b        = Money::from(2, $currency);

        $c = $a->add($b);

        $this->assertSame(3, $c->amount());
        $this->assertSame($currency, $c->currency());
    }

    public function testAnotherMoneyObjectWithSameCurrencyCanBeSubtracted(): void
    {
        $currency = Currency::from('EUR');
        $a        = Money::from(3, $currency);
        $b        = Money::from(2, $currency);

        $c = $a->subtract($b);

        $this->assertSame(1, $c->amount());
        $this->assertSame($currency, $c->currency());
    }

    public function testAnotherMoneyObjectWithDifferentCurrencyCannotBeAdded(): void
    {
        $a = Money::from(1, Currency::from('EUR'));
        $b = Money::from(2, Currency::from('GBP'));

        $this->expectException(CurrencyMismatchException::class);

        $c = $a->add($b);
    }

    public function testAnotherMoneyObjectWithDifferentCurrencyCannotBeSubtracted(): void
    {
        $a = Money::from(1, Currency::from('EUR'));
        $b = Money::from(2, Currency::from('GBP'));

        $this->expectException(CurrencyMismatchException::class);

        $c = $a->subtract($b);
    }
}
