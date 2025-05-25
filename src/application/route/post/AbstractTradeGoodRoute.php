<?php declare(strict_types=1);
namespace example\caledonia\application;

use function assert;
use function in_array;
use function is_array;
use function is_int;
use function json_decode;
use example\caledonia\domain\Good;

/**
 * @no-named-arguments
 */
abstract readonly class AbstractTradeGoodRoute
{
    /**
     * @return array{good: Good, amount: positive-int}
     */
    final protected function decode(string $json): array
    {
        $data = json_decode($json, true);

        assert(is_array($data));
        assert(isset($data['good']));
        assert(in_array($data['good'], ['bread', 'cheese', 'grain', 'milk', 'whisky', 'wool'], true));
        assert(isset($data['amount']));
        assert(is_int($data['amount']));
        assert(isset($data['amount']));
        assert(is_int($data['amount']));

        $amount = $data['amount'];

        assert($amount >= 1);

        return [
            'good'   => Good::from($data['good']),
            'amount' => $amount,
        ];
    }
}
