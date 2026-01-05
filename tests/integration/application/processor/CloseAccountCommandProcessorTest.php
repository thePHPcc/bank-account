<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingCloseAccountCommandProcessor::class)]
#[Group('integration/application')]
#[Group('integration/application/command-processor')]
#[Medium]
#[TestDox('ProcessingCloseAccountCommandProcessor')]
final class CloseAccountCommandProcessorTest extends EventTestCase
{
    #[TestDox('Emits an AccountClosedEvent when an account is closed')]
    public function testEmitsAccountClosedEvent(): void
    {
        $this->given(
            $this->accountOpened('the-owner'),
        );

        $this->when(
            $this->closeAccount(),
        );

        $this->then(
            $this->accountClosed(),
        );
    }
}
