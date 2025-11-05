<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingCloseAccountCommandProcessor::class)]
#[Medium]
final class CloseAccountCommandProcessorTest extends EventTestCase
{
    #[TestDox('An AccountClosedEvent is emitted when an account is closed')]
    public function testEmitsAccountClosedEvent(): void
    {
        $this->given(
            $this->accountOpened('the-owner'),
        );

        $this->when(new CloseAccountCommand);

        $this->then(
            $this->accountClosed(),
        );
    }
}
