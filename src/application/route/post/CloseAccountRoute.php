<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function str_replace;
use function str_starts_with;
use example\bankaccount\domain\CloseAccountCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\PostRequest;
use example\framework\http\PostRequestRoute;
use example\framework\library\InvalidUuidException;
use example\framework\library\Uuid;

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
        if (!str_starts_with($request->uri(), '/close-account/')) {
            return false;
        }

        try {
            $uuid = str_replace('/close-account/', '', $request->uri());

            if ($uuid === '') {
                return false;
            }

            $accountId = Uuid::from($uuid);
        } catch (InvalidUuidException) {
            return false;
        }

        return new CloseAccountCommand(
            $this->factory->createCloseAccountCommandProcessor(),
            new DomainCommand($accountId),
        );
    }
}
