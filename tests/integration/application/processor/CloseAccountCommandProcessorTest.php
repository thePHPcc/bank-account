<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand;
use example\framework\event\EventTestCase;
use example\framework\library\Uuid;
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

        $this->when(
            new CloseAccountCommand(
                Uuid::from('102bbf1a-fa1c-455a-b77d-5f9f99d8332e'),
            ),
        );

        $this->then(
            $this->accountClosed(),
        );
    }
}
