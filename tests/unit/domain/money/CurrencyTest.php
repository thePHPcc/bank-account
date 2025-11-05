<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
#[Small]
final class CurrencyTest extends TestCase
{
    public function testHasCurrencyCode(): void
    {
        $this->assertSame('EUR', Currency::from('EUR')->currencyCode());
    }

    public function testHasNumericCode(): void
    {
        $this->assertSame(978, Currency::from('EUR')->numericCode());
    }

    public function testHasDisplayName(): void
    {
        $this->assertSame('Euro', Currency::from('EUR')->displayName());
    }

    public function testHasDefaultFractionDigits(): void
    {
        $this->assertSame(2, Currency::from('EUR')->defaultFractionDigits());
    }

    public function testHasSubUnit(): void
    {
        $this->assertSame(100, Currency::from('EUR')->subUnit());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('EUR', (string) Currency::from('EUR'));
    }

    public function testCanBeCompared(): void
    {
        $a = Currency::from('EUR');
        $b = Currency::from('GBP');

        $this->assertTrue($a->equalTo($a));
        $this->assertFalse($a->equalTo($b));
    }

    public function testCanBeEuro(): void
    {
        $currency = Currency::from('EUR');

        $this->assertSame('EUR', $currency->currencyCode());
    }

    public function testCanBePoundSterling(): void
    {
        $currency = Currency::from('GBP');

        $this->assertSame('GBP', $currency->currencyCode());
    }

    public function testOnlySupportsEuroAndPoundSterling(): void
    {
        $this->expectException(UnsupportedCurrencyException::class);

        Currency::from('USD');
    }
}
