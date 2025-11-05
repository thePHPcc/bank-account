<?php declare(strict_types=1);
namespace example\bankaccount\domain;

/**
 * @no-named-arguments
 */
final class OverflowException extends \OverflowException implements Exception
{
}
