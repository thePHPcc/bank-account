<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccountRoute::class)]
#[Medium]
final class BankAccountRouteTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: non-empty-string}>
     */
    public static function provider(): array
    {
        return [
            ['/'],
            ['/account/'],
            ['/account/not-a-uuid'],
            ['c441fb71-cce9-4156-ba02-5f39912d1444'],
        ];
    }

    #[TestDox('Routes GET request to /account/<uuid> to BankAccountQuery')]
    public function testRoutesGetRequestForBankAccount(): void
    {
        $route = new BankAccountRoute($this->createStub(QueryFactory::class));

        $query = $route->route(GetRequest::from('/account/c441fb71-cce9-4156-ba02-5f39912d1444', []));

        $this->assertInstanceOf(BankAccountQuery::class, $query);
    }

    #[DataProvider('provider')]
    #[TestDox('Does not route GET requests to URIs other than /account/<uuid>')]
    public function testDoesNotRouteGetRequestsForOtherUris(string $uri): void
    {
        $route = new BankAccountRoute($this->createStub(QueryFactory::class));

        $query = $route->route(GetRequest::from($uri, []));

        $this->assertFalse($query);
    }
}
