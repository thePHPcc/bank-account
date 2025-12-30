<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function str_replace;
use function str_starts_with;
use example\bankaccount\domain\DepositMoneyCommand as DomainCommand;
use example\framework\http\Command;
use example\framework\http\PostRequest;
use example\framework\library\InvalidUuidException;
use example\framework\library\Uuid;

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
        if (!str_starts_with($request->uri(), '/deposit-money/')) {
            return false;
        }

        try {
            $uuid = str_replace('/deposit-money/', '', $request->uri());

            if ($uuid === '') {
                return false;
            }

            $accountId = Uuid::from($uuid);
        } catch (InvalidUuidException) {
            return false;
        }

        $data = $this->decode($request->body());

        return new DepositMoneyCommand(
            $this->factory->createDepositMoneyCommandProcessor(),
            new DomainCommand(
                $accountId,
                $data['amount'],
                $data['description'],
            ),
        );
    }
}
