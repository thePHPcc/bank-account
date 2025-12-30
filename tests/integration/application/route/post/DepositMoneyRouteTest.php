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

#[CoversClass(DepositMoneyRoute::class)]
#[Medium]
final class DepositMoneyRouteTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: non-empty-string}>
     */
    public static function provider(): array
    {
        return [
            ['/'],
            ['/deposit-money/'],
            ['/deposit-money/not-a-uuid'],
            ['1be83b92-ebf4-4a32-98ec-baecfd615aca'],
        ];
    }

    #[TestDox('Routes POST request to /deposit-money/<uuid> to DepositMoneyCommand')]
    public function testRoutesPostRequestForDepositMoney(): void
    {
        $route = new DepositMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/deposit-money/1be83b92-ebf4-4a32-98ec-baecfd615aca',
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

        $this->assertInstanceOf(DepositMoneyCommand::class, $command);
    }

    #[DataProvider('provider')]
    #[TestDox('Does not route POST request to URIs other than /deposit-money/<uuid>')]
    public function testDoesNotRoutePostRequestsForOtherUris(string $uri): void
    {
        $route = new DepositMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from($uri, ''));

        $this->assertFalse($command);
    }
}
