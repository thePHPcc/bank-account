<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function json_encode;
use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAccountRoute::class)]
#[Medium]
final class OpenAccountRouteTest extends TestCase
{
    #[TestDox('Routes POST request to /open-account to OpenAccountCommand')]
    public function testRoutesPostRequestForOpenAccount(): void
    {
        $route = new OpenAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/open-account',
                json_encode(
                    [
                        'owner' => 'the-owner',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ),
        );

        $this->assertInstanceOf(OpenAccountCommand::class, $command);
    }

    #[TestDox('Does not route POST request to URIs other than /open-account to OpenAccountCommand')]
    public function testDoesNotRoutePostRequestsForOtherUris(): void
    {
        $route = new OpenAccountRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from('/', ''));

        $this->assertFalse($command);
    }
}
