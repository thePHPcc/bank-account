<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\BankAccount;
use example\bankaccount\domain\CloseAccountCommand;
use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessingCloseAccountCommandProcessor::class)]
#[UsesClass(CloseAccountCommand::class)]
#[UsesClass(BankAccount::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/application')]
#[Group('unit/application/command-processor')]
#[Small]
#[TestDox('ProcessingCloseAccountCommandProcessor')]
final class ProcessingCloseAccountCommandProcessorTest extends TestCase
{
    #[TestDox('Emits an AccountClosed event')]
    public function testEmitsAccountClosedEvent(): void
    {
        $bankAccount = BankAccount::from(
            'the-owner',
            Money::from(0, Currency::from('EUR')),
            true,
        );

        $sourcer = $this->createStub(BankAccountSourcer::class);

        $sourcer
            ->method('source')
            ->willReturn($bankAccount)
            ->seal();

        $emitter = $this->createMock(EventEmitter::class);

        $emitter
            ->expects($this->once())
            ->method('accountClosed')
            ->seal();

        $processor = new ProcessingCloseAccountCommandProcessor($sourcer, $emitter);

        $processor->process(
            new CloseAccountCommand(
                Uuid::from('2724135b-2c94-41ab-9bab-f6e1755d925e'),
            ),
        );

        $this->assertFalse($bankAccount->isActive());
    }
}
