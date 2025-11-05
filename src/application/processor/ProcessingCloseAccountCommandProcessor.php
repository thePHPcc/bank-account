<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand;

/**
 * @no-named-arguments
 */
final readonly class ProcessingCloseAccountCommandProcessor implements CloseAccountCommandProcessor
{
    private BankAccountSourcer $sourcer;
    private EventEmitter $emitter;

    public function __construct(BankAccountSourcer $sourcer, EventEmitter $emitter)
    {
        $this->sourcer = $sourcer;
        $this->emitter = $emitter;
    }

    public function process(CloseAccountCommand $command): void
    {
        $bankAccount = $this->sourcer->source();

        $bankAccount->close();

        $this->emitter->accountClosed();
    }
}
