<?php declare(strict_types=1);
namespace example\caledonia\domain;

use InvalidArgumentException;

/**
 * @no-named-arguments
 */
final class OldAndNewPriceMustBeDifferentException extends InvalidArgumentException implements Exception
{
}
