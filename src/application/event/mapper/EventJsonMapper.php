<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\event\EventJsonMapper as FrameworkEventJsonMapper;

/**
 * @no-named-arguments
 *
 * @codeCoverageIgnore
 */
trait EventJsonMapper
{
    private function createEventJsonMapper(): FrameworkEventJsonMapper
    {
        return new FrameworkEventJsonMapper(
            [
                'banking.account-opened'  => new AccountOpenedJsonMapper,
                'banking.account-closed'  => new AccountClosedJsonMapper,
                'banking.money-deposited' => new MoneyDepositedJsonMapper,
                'banking.money-withdrawn' => new MoneyWithdrawnJsonMapper,
            ],
        );
    }
}
