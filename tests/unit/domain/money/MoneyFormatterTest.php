<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MoneyFormatter::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
final class MoneyFormatterTest extends TestCase
{
    public function testFormatsMoneyObjectAsString(): void
    {
        $formatter = new MoneyFormatter('de_DE');
        $money     = Money::from(100, Currency::from('EUR'));

        $this->assertSame("1,00\u{a0}€", $formatter->format($money));
    }
}
