<?php declare(strict_types=1);
namespace example\bankaccount\application;

/**
 * @no-named-arguments
 */
interface BankAccountProjectionReader
{
    public function read(): string;
}
