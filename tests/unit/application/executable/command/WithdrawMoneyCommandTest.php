<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\WithdrawMoneyCommand as DomainCommand;
use example\framework\http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WithdrawMoneyCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
#[TestDox('WithdrawMoneyCommand')]
final class WithdrawMoneyCommandTest extends TestCase
{
    #[TestDox('Delegates to WithdrawMoneyCommandProcessor and returns empty response')]
    public function testDelegatesToWithdrawMoneyCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand(
            Money::from(123, Currency::from('EUR')),
            'the-description',
        );

        $processor = $this->createMock(WithdrawMoneyCommandProcessor::class);

        $processor
            ->expects($this->once())
            ->method('process')
            ->with($domainCommand);

        $command = new WithdrawMoneyCommand(
            $processor,
            $domainCommand,
        );

        $response = $command->execute();

        $this->assertSame('', $response->body());
    }
}
