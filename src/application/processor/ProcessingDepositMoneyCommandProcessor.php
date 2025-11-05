<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\DepositMoneyCommand;

/**
 * @no-named-arguments
 */
final readonly class ProcessingDepositMoneyCommandProcessor implements DepositMoneyCommandProcessor
{
    private BankAccountSourcer $sourcer;
    private EventEmitter $emitter;

    public function __construct(BankAccountSourcer $sourcer, EventEmitter $emitter)
    {
        $this->sourcer = $sourcer;
        $this->emitter = $emitter;
    }

    public function process(DepositMoneyCommand $command): void
    {
        $bankAccount = $this->sourcer->source();

        $bankAccount->deposit($command->amount());

        $this->emitter->moneyDeposited($command->amount(), $command->description());
    }
}
