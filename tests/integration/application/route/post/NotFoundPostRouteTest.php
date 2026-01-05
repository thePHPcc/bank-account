<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundPostRoute::class)]
#[Group('integration/application')]
#[Group('integration/application/routing')]
#[Medium]
#[TestDox('NotFoundPostRoute')]
final class NotFoundPostRouteTest extends TestCase
{
    #[TestDox('Routes POST request not handled by other routes to NotFoundCommand')]
    public function testRoutesPostRequestToNotFoundCommand(): void
    {
        $route = new NotFoundPostRoute;

        $command = $route->route(
            PostRequest::from('/', ''),
        );

        $this->assertInstanceOf(NotFoundCommand::class, $command);
    }
}
