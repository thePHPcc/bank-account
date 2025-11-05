<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\OpenAccountCommand;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingOpenAccountCommandProcessor::class)]
#[Medium]
final class OpenAccountCommandProcessorTest extends EventTestCase
{
    #[TestDox('An AccountOpenedEvent is emitted when an account is opened')]
    public function testEmitsAccountOpenedEvent(): void
    {
        $owner = 'the-owner';

        $this->when(new OpenAccountCommand($owner));

        $this->then(
            $this->accountOpened($owner),
        );
    }
}
