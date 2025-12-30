<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\WithdrawMoneyCommand;
use example\framework\event\EventTestCase;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingWithdrawMoneyCommandProcessor::class)]
#[Medium]
final class WithdrawMoneyCommandProcessorTest extends EventTestCase
{
    #[TestDox('A MoneyWithdrawnEvent is emitted when money is withdrawn')]
    public function testEmitsMoneyDepositedEvent(): void
    {
        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $this->given(
            $this->accountOpened('the-owner'),
        );

        $this->when(
            new WithdrawMoneyCommand(
                Uuid::from('d153371b-5338-409d-bd9f-a535b471b633'),
                $amount,
                $description,
            ),
        );

        $this->then(
            $this->moneyWithdrawn($amount, $description),
        );
    }
}
