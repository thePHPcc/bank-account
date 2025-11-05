<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Query;
use example\framework\http\Response;

/**
 * @no-named-arguments
 */
final readonly class BankAccountQuery implements Query
{
    private BankAccountProjectionReader $reader;

    public function __construct(BankAccountProjectionReader $reader)
    {
        $this->reader = $reader;
    }

    public function execute(): Response
    {
        $response = new Response;

        $response->setBody($this->reader->read());

        return $response;
    }
}
