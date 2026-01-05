<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GetRequestRouter::class)]
#[UsesClass(GetRequest::class)]
#[UsesClass(Request::class)]
#[Group('unit/framework')]
#[Group('unit/framework/http')]
#[Small]
final class GetRequestRouterTest extends TestCase
{
    public function testRoutesRequest(): void
    {
        $routeThatDoesNotMatchRequest = $this->createStub(GetRequestRoute::class);

        $routeThatDoesNotMatchRequest
            ->method('route')
            ->willReturn(false);

        $query = $this->createStub(Query::class);

        $routeThatMatchesRequest = $this->createStub(GetRequestRoute::class);

        $routeThatMatchesRequest
            ->method('route')
            ->willReturn($query);

        $router = new GetRequestRouter($routeThatDoesNotMatchRequest, $routeThatMatchesRequest);

        $this->assertSame($query, $router->route(GetRequest::from('/', [])));
    }

    public function testThrowsExceptionWhenRequestCannotBeRouted(): void
    {
        $router = new GetRequestRouter;

        $this->expectException(RequestCannotBeRoutedException::class);

        $router->route(GetRequest::from('/', []));
    }
}
