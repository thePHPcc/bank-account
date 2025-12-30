<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\WithdrawMoneyCommand;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessingWithdrawMoneyCommandProcessor::class)]
#[UsesClass(WithdrawMoneyCommand::class)]
#[UsesClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class ProcessingWithdrawMoneyCommandProcessorTest extends TestCase
{
    #[TestDox('Emits an MoneyWithdrawn event')]
    public function testEmitsMoneyWithdrawnEvent(): void
    {
        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $bankAccount = BankAccount::from(
            'the-owner',
            Money::from(123, Currency::from('EUR')),
            true,
        );

        $sourcer = $this->createStub(BankAccountSourcer::class);

        $sourcer
            ->method('source')
            ->willReturn($bankAccount);

        $emitter = $this->createMock(EventEmitter::class);

        $emitter
            ->expects($this->once())
            ->method('moneyWithdrawn')
            ->with(
                $this->isInstanceOf(Uuid::class),
                $amount,
                $description,
            );

        $emitter
            ->expects($this->never())
            ->method('accountOpened');

        $emitter
            ->expects($this->never())
            ->method('accountClosed');

        $emitter
            ->expects($this->never())
            ->method('moneyDeposited');

        $processor = new ProcessingWithdrawMoneyCommandProcessor($sourcer, $emitter);

        $processor->process(
            new WithdrawMoneyCommand(
                Uuid::from('a12c97e1-73de-4036-9004-0975aa1e1780'),
                $amount,
                $description,
            ),
        );

        $this->assertObjectEquals(Money::from(0, Currency::from('EUR')), $bankAccount->balance());
    }
}
