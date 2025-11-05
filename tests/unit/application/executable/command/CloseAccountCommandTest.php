<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand as DomainCommand;
use example\framework\http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[Small]
#[TestDox('CloseAccountCommand')]
final class CloseAccountCommandTest extends TestCase
{
    #[TestDox('Delegates to CloseAccountCommandProcessor and returns empty response')]
    public function testDelegatesToCloseAccountCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand;

        $processor = $this->createMock(CloseAccountCommandProcessor::class);

        $processor
            ->expects($this->once())
            ->method('process')
            ->with($domainCommand);

        $command = new CloseAccountCommand(
            $processor,
            $domainCommand,
        );

        $response = $command->execute();

        $this->assertSame('', $response->body());
    }
}
