<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Kernel::class)]
#[UsesClass(GetRequestRouter::class)]
#[UsesClass(PostRequestRouter::class)]
#[UsesClass(Request::class)]
#[UsesClass(GetRequest::class)]
#[UsesClass(PostRequest::class)]
#[UsesClass(Response::class)]
#[Group('unit/framework')]
#[Group('unit/framework/http')]
#[Small]
final class KernelTest extends TestCase
{
    public function testProcessesGetRequestThatCanBeRouted(): void
    {
        $expectedResponse = new Response;
        $expectedResponse->setBody('body');

        $query = $this->createStub(Query::class);
        $query
            ->method('execute')
            ->willReturn($expectedResponse);

        $route = $this->createStub(GetRequestRoute::class);
        $route
            ->method('route')
            ->willReturn($query);

        $kernel = new Kernel(new GetRequestRouter($route), new PostRequestRouter);

        $response = $kernel->run(GetRequest::from('/', []));

        $this->assertSame($expectedResponse->body(), $response->body());
    }

    public function testProcessesPostRequestThatCanBeRouted(): void
    {
        $expectedResponse = new Response;
        $expectedResponse->setBody('body');

        $command = $this->createStub(Command::class);
        $command
            ->method('execute')
            ->willReturn($expectedResponse);

        $route = $this->createStub(PostRequestRoute::class);
        $route
            ->method('route')
            ->willReturn($command);

        $kernel = new Kernel(new GetRequestRouter, new PostRequestRouter($route));

        $response = $kernel->run(PostRequest::from('/', ''));

        $this->assertSame($expectedResponse->body(), $response->body());
    }

    public function testThrowsExceptionWhenRequestTypeIsNotSupported(): void
    {
        $kernel = new Kernel(new GetRequestRouter, new PostRequestRouter);

        $this->expectException(RequestCannotBeRoutedException::class);

        $kernel->run(UnsupportedRequest::create());
    }

    public function testThrowsExceptionWhenGetRequestCannotBeRouted(): void
    {
        $kernel = new Kernel(new GetRequestRouter, new PostRequestRouter);

        $this->expectException(RequestCannotBeRoutedException::class);

        $kernel->run(GetRequest::from('/', []));
    }

    public function testThrowsExceptionWhenPostRequestCannotBeRouted(): void
    {
        $kernel = new Kernel(new GetRequestRouter, new PostRequestRouter);

        $this->expectException(RequestCannotBeRoutedException::class);

        $kernel->run(PostRequest::from('/', ''));
    }
}
