<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\WithdrawMoneyCommand as DomainCommand;
use example\framework\http\Response;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WithdrawMoneyCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/application')]
#[Group('unit/application/command')]
#[Small]
#[TestDox('WithdrawMoneyCommand')]
final class WithdrawMoneyCommandTest extends TestCase
{
    #[TestDox('Delegates to WithdrawMoneyCommandProcessor and returns empty response')]
    public function testDelegatesToWithdrawMoneyCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand(
            Uuid::from('068188d6-d162-49c7-8de1-0c2fcb2aa65e'),
            Money::from(123, Currency::from('EUR')),
            'the-description',
        );

        $processor = $this->createMock(WithdrawMoneyCommandProcessor::class);

        $processor
            ->expects($this->once())
            ->method('process')
            ->with($domainCommand)
            ->seal();

        $command = new WithdrawMoneyCommand(
            $processor,
            $domainCommand,
        );

        $response = $command->execute();

        $this->assertSame('', $response->body());
    }
}
