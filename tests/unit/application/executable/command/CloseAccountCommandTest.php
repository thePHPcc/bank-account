<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand as DomainCommand;
use example\framework\http\Response;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/application')]
#[Group('unit/application/command')]
#[Small]
#[TestDox('CloseAccountCommand')]
final class CloseAccountCommandTest extends TestCase
{
    #[TestDox('Delegates to CloseAccountCommandProcessor and returns empty response')]
    public function testDelegatesToCloseAccountCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand(
            Uuid::from('ec2571d4-1785-4fed-a88d-73ba46df0961'),
        );

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
