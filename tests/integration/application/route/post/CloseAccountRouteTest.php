<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountRoute::class)]
#[Medium]
final class CloseAccountRouteTest extends TestCase
{
    #[TestDox('Routes POST request to /close-account to CloseAccountCommand')]
    public function testRoutesPostRequestForCloseAccount(): void
    {
        $route = new CloseAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/close-account',
                '',
            ),
        );

        $this->assertInstanceOf(CloseAccountCommand::class, $command);
    }

    #[TestDox('Does not route POST request to URIs other than /close-account to CloseAccountCommand')]
    public function testDoesNotRoutePostRequestsForOtherUris(): void
    {
        $route = new CloseAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from('/', ''));

        $this->assertFalse($command);
    }
}
