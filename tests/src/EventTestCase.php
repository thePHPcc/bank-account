<?php declare(strict_types=1);
namespace example\framework\event;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use function array_keys;
use function array_values;
use function assert;
use function json_encode;
use example\bankaccount\application\BankAccountEventSourcer;
use example\bankaccount\application\CloseAccountCommandProcessor;
use example\bankaccount\application\DepositMoneyCommandProcessor;
use example\bankaccount\application\DispatchingEventEmitter;
use example\bankaccount\application\OpenAccountCommandProcessor;
use example\bankaccount\application\ProcessingCloseAccountCommandProcessor;
use example\bankaccount\application\ProcessingDepositMoneyCommandProcessor;
use example\bankaccount\application\ProcessingOpenAccountCommandProcessor;
use example\bankaccount\application\ProcessingWithdrawMoneyCommandProcessor;
use example\bankaccount\application\WithdrawMoneyCommandProcessor;
use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\CloseAccountCommand;
use example\bankaccount\domain\Command;
use example\bankaccount\domain\DepositMoneyCommand;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\bankaccount\domain\OpenAccountCommand;
use example\bankaccount\domain\WithdrawMoneyCommand;
use example\framework\library\RandomUuidGenerator;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class EventTestCase extends TestCase
{
    private EventReader&Stub $reader;
    private BankAccountEventSourcer $sourcer;
    private DispatchingEventEmitter $emitter;
    private CollectingEventDispatcher $dispatcher;

    /**
     * @var list<array{className: class-string, description: non-empty-string}>
     */
    private array $given = [];

    /**
     * @var array{className: class-string, description: non-empty-string}
     */
    private array $when;

    /**
     * @var list<array{className: class-string, description: non-empty-string}>
     */
    private array $then = [];

    final protected function setUp(): void
    {
        $this->reader     = $this->createStub(EventReader::class);
        $this->sourcer    = new BankAccountEventSourcer($this->reader);
        $this->dispatcher = new CollectingEventDispatcher;

        $this->emitter = new DispatchingEventEmitter(
            $this->dispatcher,
            new RandomUuidGenerator,
        );
    }

    final protected function given(Event ...$events): void
    {
        $events = EventCollection::fromArray(array_values($events));

        $this
            ->reader
            ->method('topic')
            ->willReturn($events);

        foreach ($events as $event) {
            $this->given[] = [
                'className'   => $event::class,
                'description' => $event->asString(),
            ];
        }
    }

    final protected function when(CloseAccountCommand|DepositMoneyCommand|OpenAccountCommand|WithdrawMoneyCommand $command): void
    {
        $processor = $this->processorFor($command);

        $processor->process($command);

        $this->when = [
            'className'   => $command::class,
            'description' => $command->asString(),
        ];
    }

    final protected function then(Event ...$events): void
    {
        $events = EventCollection::fromArray(array_values($events));

        $expected = $events->asArray();
        $actual   = $this->dispatcher->events()->asArray();

        $this->assertSameSize($expected, $actual);

        foreach (array_keys($expected) as $key) {
            assert(isset($expected[$key]));
            assert(isset($actual[$key]));

            $this->assertEventObjectsAreEqual($expected[$key], $actual[$key]);
        }

        foreach ($events as $event) {
            $this->then[] = [
                'className'   => $event::class,
                'description' => $event->asString(),
            ];
        }

        $this->provideAdditionalInformation(
            json_encode(
                [
                    'given' => $this->given,
                    'when'  => $this->when,
                    'then'  => $this->then,
                ],
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
            ),
        );
    }

    /**
     * @param non-empty-string $owner
     */
    final protected function accountOpened(string $owner): AccountOpenedEvent
    {
        return new AccountOpenedEvent(
            (new RandomUuidGenerator)->generate(),
            $owner,
        );
    }

    final protected function accountClosed(): AccountClosedEvent
    {
        return new AccountClosedEvent(
            (new RandomUuidGenerator)->generate(),
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function moneyDeposited(Money $amount, string $description): MoneyDepositedEvent
    {
        return new MoneyDepositedEvent(
            (new RandomUuidGenerator)->generate(),
            $amount,
            $description,
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function moneyWithdrawn(Money $amount, string $description): MoneyWithdrawnEvent
    {
        return new MoneyWithdrawnEvent(
            (new RandomUuidGenerator)->generate(),
            $amount,
            $description,
        );
    }

    private function processorFor(Command $command): CloseAccountCommandProcessor|DepositMoneyCommandProcessor|OpenAccountCommandProcessor|WithdrawMoneyCommandProcessor
    {
        /** @phpstan-ignore match.unhandled */
        return match ($command::class) {
            OpenAccountCommand::class   => new ProcessingOpenAccountCommandProcessor($this->emitter),
            CloseAccountCommand::class  => new ProcessingCloseAccountCommandProcessor($this->sourcer, $this->emitter),
            DepositMoneyCommand::class  => new ProcessingDepositMoneyCommandProcessor($this->sourcer, $this->emitter),
            WithdrawMoneyCommand::class => new ProcessingWithdrawMoneyCommandProcessor($this->sourcer, $this->emitter),
        };
    }

    private function assertEventObjectsAreEqual(Event $expected, Event $actual): void
    {
        $this->assertInstanceOf($expected::class, $actual);
        $this->assertSame($expected->asString(), $actual->asString());
    }
}
