<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\DepositMoneyCommand;
use example\bankaccount\domain\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessingDepositMoneyCommandProcessor::class)]
#[UsesClass(DepositMoneyCommand::class)]
#[UsesClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
final class ProcessingDepositMoneyCommandProcessorTest extends TestCase
{
    #[TestDox('Emits an MoneyDeposited event')]
    public function testEmitsMoneyDepositedEvent(): void
    {
        $amount      = Money::from(123, Currency::from('EUR'));
        $description = 'the-description';

        $sourcer = $this->createStub(BankAccountSourcer::class);

        $sourcer
            ->method('source')
            ->willReturn(
                BankAccount::from(
                    'the-owner',
                    Money::from(0, Currency::from('EUR')),
                    true,
                ),
            );

        $emitter = $this->createMock(EventEmitter::class);

        $emitter
            ->expects($this->once())
            ->method('moneyDeposited')
            ->with($amount, $description);

        $emitter
            ->expects($this->never())
            ->method('accountOpened');

        $emitter
            ->expects($this->never())
            ->method('accountClosed');

        $emitter
            ->expects($this->never())
            ->method('moneyWithdrawn');

        $processor = new ProcessingDepositMoneyCommandProcessor($sourcer, $emitter);

        $processor->process(new DepositMoneyCommand($amount, $description));
    }
}
