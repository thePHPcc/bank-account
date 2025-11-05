<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\AccountClosedEvent;
use example\bankaccount\domain\AccountOpenedEvent;
use example\bankaccount\domain\Money;
use example\bankaccount\domain\MoneyDepositedEvent;
use example\bankaccount\domain\MoneyWithdrawnEvent;
use example\framework\event\EventDispatcher;
use example\framework\library\UuidGenerator;

/**
 * @no-named-arguments
 */
final readonly class DispatchingEventEmitter implements EventEmitter
{
    private EventDispatcher $dispatcher;
    private UuidGenerator $uuidGenerator;

    public function __construct(EventDispatcher $dispatcher, UuidGenerator $uuidGenerator)
    {
        $this->dispatcher    = $dispatcher;
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * @param non-empty-string $owner
     */
    public function accountOpened(string $owner): void
    {
        $this->dispatcher->dispatch(
            new AccountOpenedEvent(
                $this->uuidGenerator->generate(),
                $owner,
            ),
        );
    }

    public function accountClosed(): void
    {
        $this->dispatcher->dispatch(
            new AccountClosedEvent(
                $this->uuidGenerator->generate(),
            ),
        );
    }

    /**
     * @param non-empty-string $description
     */
    public function moneyDeposited(Money $amount, string $description): void
    {
        $this->dispatcher->dispatch(
            new MoneyDepositedEvent(
                $this->uuidGenerator->generate(),
                $amount,
                $description,
            ),
        );
    }

    /**
     * @param non-empty-string $description
     */
    public function moneyWithdrawn(Money $amount, string $description): void
    {
        $this->dispatcher->dispatch(
            new MoneyWithdrawnEvent(
                $this->uuidGenerator->generate(),
                $amount,
                $description,
            ),
        );
    }
}
