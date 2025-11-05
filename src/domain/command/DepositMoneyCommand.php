<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use function sprintf;

/**
 * @no-named-arguments
 */
final readonly class DepositMoneyCommand extends Command
{
    private Money $amount;

    /**
     * @var non-empty-string
     */
    private string $description;

    /**
     * @param non-empty-string $description
     */
    public function __construct(Money $amount, string $description)
    {
        $this->amount      = $amount;
        $this->description = $description;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        $formatter = new MoneyFormatter('de_DE');

        return sprintf(
            'Deposit %s',
            $formatter->format($this->amount),
        );
    }
}
