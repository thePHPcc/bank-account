<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;

/**
 * @no-named-arguments
 */
interface BankAccountSourcer
{
    public function source(): BankAccount;
}
