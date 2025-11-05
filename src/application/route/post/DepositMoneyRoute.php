<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\DepositMoneyCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\PostRequest;

/**
 * @no-named-arguments
 */
final readonly class DepositMoneyRoute extends AbstractTransactionRoute
{
    private CommandFactory $factory;

    public function __construct(CommandFactory $factory)
    {
        $this->factory = $factory;
    }

    public function route(PostRequest $request): Command|false
    {
        if ($request->uri() !== '/deposit-money') {
            return false;
        }

        $data = $this->decode($request->body());

        return new DepositMoneyCommand(
            $this->factory->createDepositMoneyCommandProcessor(),
            new DomainCommand(
                $data['amount'],
                $data['description'],
            ),
        );
    }
}
