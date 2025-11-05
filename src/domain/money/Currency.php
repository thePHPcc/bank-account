<?php declare(strict_types=1);
namespace example\bankaccount\domain;

final readonly class Currency
{
    /**
     * @var array<non-empty-string, array{display_name: non-empty-string, numeric_code: positive-int, default_fraction_digits: non-negative-int, sub_unit: non-negative-int}>
     */
    private const array CURRENCIES = [
        'EUR' => [
            'display_name'            => 'Euro',
            'numeric_code'            => 978,
            'default_fraction_digits' => 2,
            'sub_unit'                => 100,
        ],
        'GBP' => [
            'display_name'            => 'Pound Sterling',
            'numeric_code'            => 826,
            'default_fraction_digits' => 2,
            'sub_unit'                => 100,
        ],
    ];

    /**
     * @var non-empty-string
     */
    private string $currencyCode;

    /**
     * @param non-empty-string $currencyCode
     *
     * @throws UnsupportedCurrencyException
     */
    public static function from(string $currencyCode): self
    {
        return new self($currencyCode);
    }

    /**
     * @param non-empty-string $currencyCode
     *
     * @throws UnsupportedCurrencyException
     */
    private function __construct(string $currencyCode)
    {
        if (!isset(self::CURRENCIES[$currencyCode])) {
            throw new UnsupportedCurrencyException;
        }

        $this->currencyCode = $currencyCode;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return non-empty-string
     */
    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return non-negative-int
     */
    public function defaultFractionDigits(): int
    {
        return self::CURRENCIES[$this->currencyCode]['default_fraction_digits'];
    }

    /**
     * @return non-empty-string
     */
    public function displayName(): string
    {
        return self::CURRENCIES[$this->currencyCode]['display_name'];
    }

    /**
     * @return positive-int
     */
    public function numericCode(): int
    {
        return self::CURRENCIES[$this->currencyCode]['numeric_code'];
    }

    /**
     * @return non-negative-int
     */
    public function subUnit(): int
    {
        return self::CURRENCIES[$this->currencyCode]['sub_unit'];
    }

    public function equalTo(self $other): bool
    {
        return $this->currencyCode === $other->currencyCode();
    }
}
