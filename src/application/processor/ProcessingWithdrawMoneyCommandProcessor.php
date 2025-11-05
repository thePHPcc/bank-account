<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\WithdrawMoneyCommand;

/**
 * @no-named-arguments
 */
final readonly class ProcessingWithdrawMoneyCommandProcessor implements WithdrawMoneyCommandProcessor
{
    private BankAccountSourcer $sourcer;
    private EventEmitter $emitter;

    public function __construct(BankAccountSourcer $sourcer, EventEmitter $emitter)
    {
        $this->sourcer = $sourcer;
        $this->emitter = $emitter;
    }

    public function process(WithdrawMoneyCommand $command): void
    {
        $bankAccount = $this->sourcer->source();

        $bankAccount->withdraw($command->amount());

        $this->emitter->moneyWithdrawn($command->amount(), $command->description());
    }
}
