<?php declare(strict_types=1);
namespace example\framework\event;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use function array_keys;
use function array_values;
use function assert;
use function json_encode;
use example\bankaccount\application\BankAccountEventSourcer;
use example\bankaccount\application\BankAccountHtmlProjector;
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
use example\bankaccount\domain\DepositMoneyCommand;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\bankaccount\domain\OpenAccountCommand;
use example\bankaccount\domain\WithdrawMoneyCommand;
use example\framework\library\RandomUuidGenerator;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class EventTestCase extends TestCase
{
    private EventReader&Stub $reader;
    private BankAccountEventSourcer $sourcer;
    private DispatchingEventEmitter $emitter;
    private CollectingEventDispatcher $dispatcher;
    private Uuid $accountId;

    /**
     * @var list<array{className: class-string, description: non-empty-string}>
     */
    private array $given = [];

    /**
     * @var array{className: class-string, description: non-empty-string}
     */
    private array $when;

    /**
     * @var ?class-string
     */
    private ?string $projector = null;

    #[Before]
    final protected function prepareEventSystem(): void
    {
        $this->reader     = $this->createStub(EventReader::class);
        $this->sourcer    = new BankAccountEventSourcer($this->reader);
        $this->dispatcher = new CollectingEventDispatcher;

        $this->emitter = new DispatchingEventEmitter(
            $this->dispatcher,
            new RandomUuidGenerator,
        );
    }

    #[After]
    final protected function provideAdditionalInformationForProjectorTests(): void
    {
        if ($this->projector === null) {
            return;
        }

        $this->provideAdditionalInformation(
            json_encode(
                [
                    'given'     => $this->given,
                    'projector' => $this->projector,
                ],
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
            ),
        );
    }

    final protected function given(Event ...$events): void
    {
        $events = EventCollection::fromArray(array_values($events));

        $this
            ->reader
            ->method('correlationId')
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

        $then = [];

        foreach ($events as $event) {
            $then[] = [
                'className'   => $event::class,
                'description' => $event->asString(),
            ];
        }

        $this->provideAdditionalInformation(
            json_encode(
                [
                    'given' => $this->given,
                    'when'  => $this->when,
                    'then'  => $then,
                ],
                JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
            ),
        );
    }

    /**
     * @param non-empty-string $owner
     */
    final protected function openAccount(string $owner): OpenAccountCommand
    {
        return new OpenAccountCommand($owner);
    }

    /**
     * @param non-empty-string $owner
     */
    final protected function accountOpened(string $owner): AccountOpenedEvent
    {
        $this->accountId = (new RandomUuidGenerator)->generate();

        return new AccountOpenedEvent(
            $this->accountId,
            $this->accountId,
            $owner,
        );
    }

    final protected function closeAccount(): CloseAccountCommand
    {
        assert(isset($this->accountId));

        return new CloseAccountCommand($this->accountId);
    }

    final protected function accountClosed(): AccountClosedEvent
    {
        assert(isset($this->accountId));

        return new AccountClosedEvent(
            (new RandomUuidGenerator)->generate(),
            $this->accountId,
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function depositMoney(Money $amount, string $description): DepositMoneyCommand
    {
        assert(isset($this->accountId));

        return new DepositMoneyCommand(
            $this->accountId,
            $amount,
            $description,
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function moneyDeposited(Money $amount, string $description): MoneyDepositedEvent
    {
        assert(isset($this->accountId));

        return new MoneyDepositedEvent(
            (new RandomUuidGenerator)->generate(),
            $this->accountId,
            $amount,
            $description,
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function withdrawMoney(Money $amount, string $description): WithdrawMoneyCommand
    {
        assert(isset($this->accountId));

        return new WithdrawMoneyCommand(
            $this->accountId,
            $amount,
            $description,
        );
    }

    /**
     * @param non-empty-string $description
     */
    final protected function moneyWithdrawn(Money $amount, string $description): MoneyWithdrawnEvent
    {
        assert(isset($this->accountId));

        return new MoneyWithdrawnEvent(
            (new RandomUuidGenerator)->generate(),
            $this->accountId,
            $amount,
            $description,
        );
    }

    final protected function bankAccountHtmlProjector(): BankAccountHtmlProjector
    {
        $this->projector = BankAccountHtmlProjector::class;

        return new BankAccountHtmlProjector($this->reader);
    }

    private function processorFor(CloseAccountCommand|DepositMoneyCommand|OpenAccountCommand|WithdrawMoneyCommand $command): CloseAccountCommandProcessor|DepositMoneyCommandProcessor|OpenAccountCommandProcessor|WithdrawMoneyCommandProcessor
    {
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
