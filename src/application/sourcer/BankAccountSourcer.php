<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
interface BankAccountSourcer
{
    public function source(Uuid $accountId): BankAccount;
}
