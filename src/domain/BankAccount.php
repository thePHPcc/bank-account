<?php declare(strict_types=1);
namespace example\bankaccount\domain;

final class BankAccount
{
    /**
     * @var non-empty-string
     */
    private readonly string $owner;
    private Money $balance;
    private bool $active;

    /**
     * @param non-empty-string $owner
     */
    public static function from(string $owner, Money $balance, bool $active): self
    {
        return new self($owner, $balance, $active);
    }

    /**
     * @param non-empty-string $owner
     */
    private function __construct(string $owner, Money $balance, bool $active)
    {
        $this->owner   = $owner;
        $this->balance = $balance;
        $this->active  = $active;
    }

    /**
     * @throws AccountIsClosedException
     * @throws AmountMustBePositiveException
     */
    public function deposit(Money $amount): void
    {
        $this->ensureAccountIsActive();
        $this->ensureAmountIsPositive($amount);

        $this->balance = $this->balance->plus($amount);
    }

    /**
     * @throws AccountIsClosedException
     * @throws AmountMustBePositiveException
     */
    public function withdraw(Money $amount): void
    {
        $this->ensureAccountIsActive();
        $this->ensureAmountIsPositive($amount);

        $this->balance = $this->balance->minus($amount);
    }

    /**
     * @throws AccountIsClosedException
     */
    public function close(): void
    {
        $this->ensureAccountIsActive();

        $this->active = false;
    }

    /**
     * @return non-empty-string
     */
    public function owner(): string
    {
        return $this->owner;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    private function ensureAccountIsActive(): void
    {
        if (!$this->isActive()) {
            throw new AccountIsClosedException;
        }
    }

    /**
     * @throws AmountMustBePositiveException
     */
    private function ensureAmountIsPositive(Money $amount): void
    {
        if ($amount->amount() <= 0) {
            throw new AmountMustBePositiveException;
        }
    }
}
