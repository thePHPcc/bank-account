<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use LogicException;

/**
 * @no-named-arguments
 */
final class AccountIsClosedException extends LogicException implements Exception
{
}
