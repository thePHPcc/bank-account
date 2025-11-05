<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\DepositMoneyCommand;

/**
 * @no-named-arguments
 */
interface DepositMoneyCommandProcessor
{
    public function process(DepositMoneyCommand $command): void;
}
