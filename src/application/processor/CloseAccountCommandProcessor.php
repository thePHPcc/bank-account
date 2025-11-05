<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand;

/**
 * @no-named-arguments
 */
interface CloseAccountCommandProcessor
{
    public function process(CloseAccountCommand $command): void;
}
