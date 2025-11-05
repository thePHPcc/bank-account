<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\WithdrawMoneyCommand;

/**
 * @no-named-arguments
 */
interface WithdrawMoneyCommandProcessor
{
    public function process(WithdrawMoneyCommand $command): void;
}
