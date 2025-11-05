<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function assert;
use function is_array;
use function is_int;
use function is_string;
use function json_decode;
use function strlen;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\framework\http\PostRequestRoute;

/**
 * @no-named-arguments
 */
abstract readonly class AbstractTransactionRoute implements PostRequestRoute
{
    /**
     * @return array{amount: Money, description: non-empty-string}
     */
    final protected function decode(string $json): array
    {
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);

        assert(is_array($data));
        assert(isset($data['amount']));
        assert(is_int($data['amount']));
        assert($data['amount'] > 0);
        assert(isset($data['currency']));
        assert(is_string($data['currency']));
        assert(isset($data['currency']));
        assert(is_string($data['currency']));
        assert(strlen($data['currency']) === 3);
        assert(isset($data['description']));
        assert(is_string($data['description']));
        assert($data['description'] !== '');

        return [
            'amount'      => Money::from($data['amount'], Currency::from($data['currency'])),
            'description' => $data['description'],
        ];
    }
}
