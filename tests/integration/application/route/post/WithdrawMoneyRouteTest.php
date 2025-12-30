<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function json_encode;
use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(WithdrawMoneyRoute::class)]
#[Medium]
final class WithdrawMoneyRouteTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: non-empty-string}>
     */
    public static function provider(): array
    {
        return [
            ['/'],
            ['/withdraw-money/'],
            ['/withdraw-money/not-a-uuid'],
            ['d64fe005-b2d8-4107-925b-6fe4ff99d6e0'],
        ];
    }

    #[TestDox('Routes POST request to /withdraw-money/<uuid> to WithdrawMoneyCommand')]
    public function testRoutesPostRequestForWithdrawMoney(): void
    {
        $route = new WithdrawMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/withdraw-money/d64fe005-b2d8-4107-925b-6fe4ff99d6e0',
                json_encode(
                    [
                        'amount'      => 123,
                        'currency'    => 'EUR',
                        'description' => 'the-description',
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ),
        );

        $this->assertInstanceOf(WithdrawMoneyCommand::class, $command);
    }

    #[DataProvider('provider')]
    #[TestDox('Does not route POST request to URIs other than /withdraw-money/<uuid>')]
    public function testDoesNotRoutePostRequestsForOtherUris(string $uri): void
    {
        $route = new WithdrawMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from($uri, ''));

        $this->assertFalse($command);
    }
}
