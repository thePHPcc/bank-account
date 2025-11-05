<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\OpenAccountCommand as DomainCommand;
use example\framework\http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAccountCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[Small]
#[TestDox('OpenAccountCommand')]
final class OpenAccountCommandTest extends TestCase
{
    #[TestDox('Delegates to OpenAccountCommandProcessor and returns empty response')]
    public function testDelegatesToOpenAccountCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand('the-owner');

        $processor = $this->createMock(OpenAccountCommandProcessor::class);

        $processor
            ->expects($this->once())
            ->method('process')
            ->with($domainCommand);

        $command = new OpenAccountCommand(
            $processor,
            $domainCommand,
        );

        $response = $command->execute();

        $this->assertSame('', $response->body());
    }
}
