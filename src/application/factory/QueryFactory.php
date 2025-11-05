<?php declare(strict_types=1);
namespace example\bankaccount\application;

/**
 * @no-named-arguments
 */
interface QueryFactory
{
    public function createBankAccountHtmlProjectionReader(): BankAccountProjectionReader;
}
