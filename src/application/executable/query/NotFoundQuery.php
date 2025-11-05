<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Query;
use example\framework\http\Response;

/**
 * @no-named-arguments
 */
final readonly class NotFoundQuery implements Query
{
    public function execute(): Response
    {
        $response = new Response;

        $response->setBody('not found');

        return $response;
    }
}
