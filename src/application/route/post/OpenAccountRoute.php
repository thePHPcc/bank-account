<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function assert;
use function is_array;
use function is_string;
use function json_decode;
use example\bankaccount\domain\OpenAccountCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\PostRequest;
use example\framework\http\PostRequestRoute;

/**
 * @no-named-arguments
 */
final readonly class OpenAccountRoute implements PostRequestRoute
{
    private CommandFactory $factory;

    public function __construct(CommandFactory $factory)
    {
        $this->factory = $factory;
    }

    public function route(PostRequest $request): Command|false
    {
        if ($request->uri() !== '/open-account') {
            return false;
        }

        $data = json_decode($request->body(), true, JSON_THROW_ON_ERROR);

        assert(is_array($data));
        assert(isset($data['owner']));
        assert(is_string($data['owner']));
        assert($data['owner'] !== '');

        return new OpenAccountCommand(
            $this->factory->createOpenAccountCommandProcessor(),
            new DomainCommand($data['owner']),
        );
    }
}
