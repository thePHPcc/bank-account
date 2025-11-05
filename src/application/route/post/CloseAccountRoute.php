<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\CloseAccountCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\PostRequest;
use example\framework\http\PostRequestRoute;

/**
 * @no-named-arguments
 */
final readonly class CloseAccountRoute implements PostRequestRoute
{
    private CommandFactory $factory;

    public function __construct(CommandFactory $factory)
    {
        $this->factory = $factory;
    }

    public function route(PostRequest $request): Command|false
    {
        if ($request->uri() !== '/close-account') {
            return false;
        }

        return new CloseAccountCommand(
            $this->factory->createCloseAccountCommandProcessor(),
            new DomainCommand,
        );
    }
}
