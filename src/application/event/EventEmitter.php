<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Money;

/**
 * @no-named-arguments
 */
interface EventEmitter
{
    /**
     * @param non-empty-string $owner
     */
    public function accountOpened(string $owner): void;

    public function accountClosed(): void;

    /**
     * @param non-empty-string $description
     */
    public function moneyDeposited(Money $amount, string $description): void;

    /**
     * @param non-empty-string $description
     */
    public function moneyWithdrawn(Money $amount, string $description): void;
}
