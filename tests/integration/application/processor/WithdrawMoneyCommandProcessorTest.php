<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingWithdrawMoneyCommandProcessor::class)]
#[Group('integration/application')]
#[Group('integration/application/command-processor')]
#[TestDox('ProcessingWithdrawMoneyCommandProcessor')]
#[Medium]
final class WithdrawMoneyCommandProcessorTest extends EventTestCase
{
    #[TestDox('Emits a MoneyWithdrawnEvent when money is withdrawn')]
    public function testEmitsMoneyDepositedEvent(): void
    {
        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $this->given(
            $this->accountOpened('the-owner'),
        );

        $this->when(
            $this->withdrawMoney(
                $amount,
                $description,
            ),
        );

        $this->then(
            $this->moneyWithdrawn($amount, $description),
        );
    }
}
