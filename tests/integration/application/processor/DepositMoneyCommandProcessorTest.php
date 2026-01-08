<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingDepositMoneyCommandProcessor::class)]
#[Group('integration/application')]
#[Group('integration/application/command-processor')]
#[Group('visual-documentation')]
#[Medium]
#[TestDox('ProcessingDepositMoneyCommandProcessor')]
final class DepositMoneyCommandProcessorTest extends EventTestCase
{
    #[TestDox('Emits a MoneyDepositedEvent when money is deposited')]
    public function testEmitsMoneyDepositedEvent(): void
    {
        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $this->given(
            $this->accountOpened('the-owner'),
        );

        $this->when(
            $this->depositMoney(
                $amount,
                $description,
            ),
        );

        $this->then(
            $this->moneyDeposited($amount, $description),
        );
    }
}
