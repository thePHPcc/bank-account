<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\Response;

/**
 * @no-named-arguments
 */
final readonly class CloseAccountCommand implements Command
{
    private CloseAccountCommandProcessor $processor;
    private DomainCommand $command;

    public function __construct(CloseAccountCommandProcessor $processor, DomainCommand $command)
    {
        $this->processor = $processor;
        $this->command   = $command;
    }

    public function execute(): Response
    {
        $this->processor->process($this->command);

        return new Response;
    }
}
