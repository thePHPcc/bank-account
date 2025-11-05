<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use function assert;
use function is_string;
use NumberFormatter;

final readonly class MoneyFormatter
{
    private NumberFormatter $numberFormatter;

    /**
     * @param non-empty-string $locale
     */
    public function __construct(string $locale)
    {
        $this->numberFormatter = new NumberFormatter(
            $locale,
            NumberFormatter::CURRENCY,
        );
    }

    /**
     * @return non-empty-string
     */
    public function format(Money $money): string
    {
        $buffer = $this->numberFormatter->formatCurrency(
            $money->convertedAmount(),
            $money->currency()->currencyCode(),
        );

        assert(is_string($buffer));
        assert($buffer !== '');

        return $buffer;
    }
}
