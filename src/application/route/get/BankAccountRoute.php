<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use example\framework\http\GetRequestRoute;
use example\framework\http\Query;

/**
 * @no-named-arguments
 */
final readonly class BankAccountRoute implements GetRequestRoute
{
    private QueryFactory $factory;

    public function __construct(QueryFactory $factory)
    {
        $this->factory = $factory;
    }

    public function route(GetRequest $request): false|Query
    {
        if ($request->uri() !== '/bank-account') {
            return false;
        }

        return new BankAccountQuery(
            $this->factory->createBankAccountHtmlProjectionReader(),
        );
    }
}
