<?php declare(strict_types=1);
namespace example\bankaccount\application;

/**
 * @no-named-arguments
 *
 * @codeCoverageIgnore
 */
final readonly class ProductionQueryFactory implements QueryFactory
{
    use EventReading;

    public function createBankAccountHtmlProjectionReader(): BankAccountProjectionReader
    {
        return new FilesystemBankAccountProjectionReader(
            __DIR__ . '/../../../tests/expectation/bank-account.html',
        );
    }
}
