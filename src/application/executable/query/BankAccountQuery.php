<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Query;
use example\framework\http\Response;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class BankAccountQuery implements Query
{
    private BankAccountProjectionReader $reader;
    private Uuid $accountId;

    public function __construct(BankAccountProjectionReader $reader, Uuid $accountId)
    {
        $this->reader    = $reader;
        $this->accountId = $accountId;
    }

    public function execute(): Response
    {
        $response = new Response;

        $response->setBody($this->reader->read($this->accountId));

        return $response;
    }
}
