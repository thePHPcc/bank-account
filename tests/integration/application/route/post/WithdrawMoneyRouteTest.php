<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function json_encode;
use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(WithdrawMoneyRoute::class)]
#[Medium]
final class WithdrawMoneyRouteTest extends TestCase
{
    #[TestDox('Routes POST request to /withdraw-money to WithdrawMoneyCommand')]
    public function testRoutesPostRequestForWithdrawMoney(): void
    {
        $route = new WithdrawMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/withdraw-money',
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

    #[TestDox('Does not route POST request to URIs other than /withdraw-money to WithdrawMoneyCommand')]
    public function testDoesNotRoutePostRequestsForOtherUris(): void
    {
        $route = new WithdrawMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from('/', ''));

        $this->assertFalse($command);
    }
}
