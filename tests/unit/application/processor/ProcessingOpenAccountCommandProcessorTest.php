<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\OpenAccountCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessingOpenAccountCommandProcessor::class)]
#[UsesClass(OpenAccountCommand::class)]
#[Small]
final class ProcessingOpenAccountCommandProcessorTest extends TestCase
{
    #[TestDox('Emits an AccountOpened event')]
    public function testEmitsAccountOpenedEvent(): void
    {
        $owner = 'the-owner';

        $emitter = $this->createMock(EventEmitter::class);

        $emitter
            ->expects($this->once())
            ->method('accountOpened')
            ->with($owner);

        $emitter
            ->expects($this->never())
            ->method('accountClosed');

        $emitter
            ->expects($this->never())
            ->method('moneyDeposited');

        $emitter
            ->expects($this->never())
            ->method('moneyWithdrawn');

        $processor = new ProcessingOpenAccountCommandProcessor($emitter);

        $processor->process(new OpenAccountCommand($owner));
    }
}
