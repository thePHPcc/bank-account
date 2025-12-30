<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
interface BankAccountProjectionReader
{
    public function read(Uuid $accountId): string;
}
