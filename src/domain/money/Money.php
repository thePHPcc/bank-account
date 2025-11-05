<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use function round;

final readonly class Money
{
    private int $amount;
    private Currency $currency;

    public static function from(int $amount, Currency $currency): self
    {
        return new self($amount, $currency);
    }

    private function __construct(int $amount, Currency $currency)
    {
        $this->amount   = $amount;
        $this->currency = $currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function convertedAmount(): float
    {
        return round($this->amount / $this->currency->subUnit(), $this->currency->defaultFractionDigits());
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($this, $other);

        $value = $this->amount + $other->amount();

        return $this->newMoney($value);
    }

    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($this, $other);

        $value = $this->amount - $other->amount();

        return $this->newMoney($value);
    }

    public function negate(): self
    {
        $value = -1 * $this->amount;

        return $this->newMoney($value);
    }

    private function ensureSameCurrency(self $a, self $b): void
    {
        if (!$a->currency()->equalTo($b->currency())) {
            throw new CurrencyMismatchException;
        }
    }

    private function newMoney(int $amount): self
    {
        return new self($amount, $this->currency);
    }
}
