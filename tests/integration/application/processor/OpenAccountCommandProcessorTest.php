<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingOpenAccountCommandProcessor::class)]
#[Group('integration/application')]
#[Group('integration/application/command-processor')]
#[Group('visual-documentation')]
#[Medium]
#[TestDox('ProcessingOpenAccountCommandProcessor')]
final class OpenAccountCommandProcessorTest extends EventTestCase
{
    #[TestDox('Emits an AccountOpenedEvent when an account is opened')]
    public function testEmitsAccountOpenedEvent(): void
    {
        $owner = 'the-owner';

        $this->when(
            $this->openAccount($owner),
        );

        $this->then(
            $this->accountOpened($owner),
        );
    }
}
