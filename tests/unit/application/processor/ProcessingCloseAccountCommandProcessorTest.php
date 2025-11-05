<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\CloseAccountCommand;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessingCloseAccountCommandProcessor::class)]
#[UsesClass(CloseAccountCommand::class)]
#[UsesClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
final class ProcessingCloseAccountCommandProcessorTest extends TestCase
{
    #[TestDox('Emits an AccountClosed event')]
    public function testEmitsAccountClosedEvent(): void
    {
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
            ->method('accountClosed');

        $emitter
            ->expects($this->never())
            ->method('accountOpened');

        $emitter
            ->expects($this->never())
            ->method('moneyDeposited');

        $emitter
            ->expects($this->never())
            ->method('moneyWithdrawn');

        $processor = new ProcessingCloseAccountCommandProcessor($sourcer, $emitter);

        $processor->process(new CloseAccountCommand);
    }
}
