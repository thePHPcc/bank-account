<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostRequestRouter::class)]
#[UsesClass(PostRequest::class)]
#[UsesClass(Request::class)]
#[Group('unit/framework')]
#[Group('unit/framework/http')]
#[Small]
final class PostRequestRouterTest extends TestCase
{
    public function testRoutesRequest(): void
    {
        $routeThatDoesNotMatchRequest = $this->createStub(PostRequestRoute::class);

        $routeThatDoesNotMatchRequest
            ->method('route')
            ->willReturn(false);

        $command = $this->createStub(Command::class);

        $routeThatMatchesRequest = $this->createStub(PostRequestRoute::class);

        $routeThatMatchesRequest
            ->method('route')
            ->willReturn($command);

        $router = new PostRequestRouter($routeThatDoesNotMatchRequest, $routeThatMatchesRequest);

        $this->assertSame($command, $router->route(PostRequest::from('/', '')));
    }

    public function testThrowsExceptionWhenRequestCannotBeRouted(): void
    {
        $router = new PostRequestRouter;

        $this->expectException(RequestCannotBeRoutedException::class);

        $router->route(PostRequest::from('/', ''));
    }
}
