<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Command;
use example\framework\http\PostRequest;
use example\framework\http\PostRequestRoute;

/**
 * @no-named-arguments
 */
final readonly class NotFoundPostRoute implements PostRequestRoute
{
    public function route(PostRequest $request): Command
    {
        return new NotFoundCommand;
    }
}
