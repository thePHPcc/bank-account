<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use InvalidArgumentException;

/**
 * @no-named-arguments
 */
final class CurrencyMismatchException extends InvalidArgumentException implements Exception
{
}
