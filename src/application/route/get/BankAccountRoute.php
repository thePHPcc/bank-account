<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function str_replace;
use function str_starts_with;
use example\framework\http\GetRequest;
use example\framework\http\GetRequestRoute;
use example\framework\http\Query;
use example\framework\library\InvalidUuidException;
use example\framework\library\Uuid;

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
        if (!str_starts_with($request->uri(), '/account/')) {
            return false;
        }

        try {
            $uuid = str_replace('/account/', '', $request->uri());

            if ($uuid === '') {
                return false;
            }

            $accountId = Uuid::from($uuid);
        } catch (InvalidUuidException) {
            return false;
        }

        return new BankAccountQuery(
            $this->factory->createBankAccountHtmlProjectionReader(),
            $accountId,
        );
    }
}
