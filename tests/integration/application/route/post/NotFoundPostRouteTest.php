<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundPostRoute::class)]
#[Medium]
final class NotFoundPostRouteTest extends TestCase
{
    #[TestDox('Routes POST request to NotFoundCommand')]
    public function testRoutesPostRequestToNotFoundCommand(): void
    {
        $route = new NotFoundPostRoute;

        $command = $route->route(
            PostRequest::from('/', ''),
        );

        $this->assertInstanceOf(NotFoundCommand::class, $command);
    }
}
