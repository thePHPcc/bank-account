<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountRoute::class)]
#[Medium]
final class CloseAccountRouteTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: non-empty-string}>
     */
    public static function provider(): array
    {
        return [
            ['/'],
            ['/close-account/'],
            ['/close-account/not-a-uuid'],
            ['8824fca0-e695-4fb1-9c3b-ade4ef86e5e8'],
        ];
    }

    #[TestDox('Routes POST request to /close-account/<uuid> to CloseAccountCommand')]
    public function testRoutesPostRequestForCloseAccount(): void
    {
        $route = new CloseAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/close-account/8824fca0-e695-4fb1-9c3b-ade4ef86e5e8',
                '',
            ),
        );

        $this->assertInstanceOf(CloseAccountCommand::class, $command);
    }

    #[DataProvider('provider')]
    #[TestDox('Does not route POST request to URIs other than /close-account/<uuid>')]
    public function testDoesNotRoutePostRequestsForOtherUris(string $uri): void
    {
        $route = new CloseAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from($uri, ''));

        $this->assertFalse($command);
    }
}
