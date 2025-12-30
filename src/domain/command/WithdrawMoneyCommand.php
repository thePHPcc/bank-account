<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use function sprintf;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class WithdrawMoneyCommand extends Command
{
    private Uuid $accountId;
    private Money $amount;

    /**
     * @var non-empty-string
     */
    private string $description;

    /**
     * @param non-empty-string $description
     */
    public function __construct(Uuid $accountId, Money $amount, string $description)
    {
        $this->accountId   = $accountId;
        $this->amount      = $amount;
        $this->description = $description;
    }

    public function accountId(): Uuid
    {
        return $this->accountId;
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
            'Withdraw %s',
            $formatter->format($this->amount),
        );
    }
}
