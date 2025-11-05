<?php declare(strict_types=1);
namespace example\bankaccount\application;

use const JSON_THROW_ON_ERROR;
use function json_encode;
use example\framework\http\PostRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(DepositMoneyRoute::class)]
#[Medium]
final class DepositMoneyRouteTest extends TestCase
{
    #[TestDox('Routes POST request to /deposit-money to DepositMoneyCommand')]
    public function testRoutesPostRequestForDepositMoney(): void
    {
        $route = new DepositMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(
            PostRequest::from(
                '/deposit-money',
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

    #[TestDox('Does not route POST request to URIs other than /deposit-money to DepositMoneyCommand')]
    public function testDoesNotRoutePostRequestsForOtherUris(): void
    {
        $route = new DepositMoneyRoute($this->createStub(CommandFactory::class));

        $command = $route->route(PostRequest::from('/', ''));

        $this->assertFalse($command);
    }
}
