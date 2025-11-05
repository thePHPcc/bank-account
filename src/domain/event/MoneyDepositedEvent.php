<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use function sprintf;
use example\framework\event\Event;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class MoneyDepositedEvent extends Event
{
    private Money $amount;
    private string $description;

    /**
     * @param non-empty-string $description
     */
    public function __construct(Uuid $id, Money $amount, string $description)
    {
        parent::__construct($id);

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
    public function topic(): string
    {
        return 'banking.money-deposited';
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        $formatter = new MoneyFormatter('de_DE');

        return sprintf(
            '%s deposited',
            $formatter->format($this->amount),
        );
    }
}
