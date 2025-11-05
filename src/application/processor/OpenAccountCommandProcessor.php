<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\OpenAccountCommand;

/**
 * @no-named-arguments
 */
interface OpenAccountCommandProcessor
{
    public function process(OpenAccountCommand $command): void;
}
