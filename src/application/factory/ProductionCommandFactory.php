<?php declare(strict_types=1);
namespace example\bankaccount\application;

/**
 * @no-named-arguments
 *
 * @codeCoverageIgnore
 */
final readonly class ProductionCommandFactory implements CommandFactory
{
    use EventReading;
    use EventWriting;

    public function createOpenAccountCommandProcessor(): OpenAccountCommandProcessor
    {
        return new ProcessingOpenAccountCommandProcessor(
            $this->createEventEmitter(),
        );
    }

    public function createCloseAccountCommandProcessor(): CloseAccountCommandProcessor
    {
        return new ProcessingCloseAccountCommandProcessor(
            $this->createBankAccountEventSourcer(),
            $this->createEventEmitter(),
        );
    }

    public function createDepositMoneyCommandProcessor(): DepositMoneyCommandProcessor
    {
        return new ProcessingDepositMoneyCommandProcessor(
            $this->createBankAccountEventSourcer(),
            $this->createEventEmitter(),
        );
    }

    public function createWithdrawMoneyCommandProcessor(): WithdrawMoneyCommandProcessor
    {
        return new ProcessingWithdrawMoneyCommandProcessor(
            $this->createBankAccountEventSourcer(),
            $this->createEventEmitter(),
        );
    }
}
