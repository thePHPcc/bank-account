<?php declare(strict_types=1);
namespace example\framework\event;

use example\bankaccount\domain\AmountMustBePositiveException;
use example\bankaccount\domain\Money;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
abstract readonly class Event
{
    private Uuid $id;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }

    final public function id(): Uuid
    {
        return $this->id;
    }

    /**
     * @return non-empty-string
     */
    abstract public function topic(): string;

    /**
     * @return non-empty-string
     */
    abstract public function asString(): string;

    /**
     * @throws AmountMustBePositiveException
     */
    protected function ensureAmountIsPositive(Money $amount): void
    {
        if ($amount->isPositive()) {
            return;
        }

        throw new AmountMustBePositiveException;
    }
}
