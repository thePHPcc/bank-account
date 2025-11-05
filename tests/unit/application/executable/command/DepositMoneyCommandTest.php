<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\Currency;
use example\bankaccount\domain\DepositMoneyCommand as DomainCommand;
use example\bankaccount\domain\Money;
use example\framework\http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DepositMoneyCommand::class)]
#[UsesClass(DomainCommand::class)]
#[UsesClass(Response::class)]
#[UsesClass(Money::class)]
#[UsesClass(Currency::class)]
#[Small]
#[TestDox('DepositMoneyCommand')]
final class DepositMoneyCommandTest extends TestCase
{
    #[TestDox('Delegates to DepositMoneyCommandProcessor and returns empty response')]
    public function testDelegatesToDepositMoneyCommandProcessorAndReturnsEmptyResponse(): void
    {
        $domainCommand = new DomainCommand(
            Money::from(123, Currency::from('EUR')),
            'the-description',
        );

        $processor = $this->createMock(DepositMoneyCommandProcessor::class);

        $processor
            ->expects($this->once())
            ->method('process')
            ->with($domainCommand);

        $command = new DepositMoneyCommand(
            $processor,
            $domainCommand,
        );

        $response = $command->execute();

        $this->assertSame('', $response->body());
    }
}
