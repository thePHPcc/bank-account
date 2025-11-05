<?php declare(strict_types=1);
namespace example\bankaccount\application;

/**
 * @no-named-arguments
 */
interface CommandFactory
{
    public function createOpenAccountCommandProcessor(): OpenAccountCommandProcessor;

    public function createCloseAccountCommandProcessor(): CloseAccountCommandProcessor;

    public function createDepositMoneyCommandProcessor(): DepositMoneyCommandProcessor;

    public function createWithdrawMoneyCommandProcessor(): WithdrawMoneyCommandProcessor;
}
