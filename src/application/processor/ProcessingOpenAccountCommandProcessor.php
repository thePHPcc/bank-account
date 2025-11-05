<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\OpenAccountCommand;

/**
 * @no-named-arguments
 */
final readonly class ProcessingOpenAccountCommandProcessor implements OpenAccountCommandProcessor
{
    private EventEmitter $emitter;

    public function __construct(EventEmitter $emitter)
    {
        $this->emitter = $emitter;
    }

    public function process(OpenAccountCommand $command): void
    {
        $this->emitter->accountOpened($command->owner());
    }
}
