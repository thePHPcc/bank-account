<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Money;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
interface EventEmitter
{
    /**
     * @param non-empty-string $owner
     */
    public function accountOpened(string $owner): void;

    public function accountClosed(Uuid $accountId): void;

    /**
     * @param non-empty-string $description
     */
    public function moneyDeposited(Uuid $accountId, Money $amount, string $description): void;

    /**
     * @param non-empty-string $description
     */
    public function moneyWithdrawn(Uuid $accountId, Money $amount, string $description): void;
}
