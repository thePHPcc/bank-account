<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccountRoute::class)]
#[Medium]
final class BankAccountRouteTest extends TestCase
{
    #[TestDox('Routes GET request to /bank-account to BankAccountQuery')]
    public function testRoutesGetRequestForBankAccount(): void
    {
        $route = new BankAccountRoute($this->createStub(QueryFactory::class));

        $query = $route->route(GetRequest::from('/bank-account', []));

        $this->assertInstanceOf(BankAccountQuery::class, $query);
    }

    #[TestDox('Does not route GET requests to URIs other than /bank-account to BankAccountQuery')]
    public function testDoesNotRouteGetRequestsForOtherUris(): void
    {
        $route = new BankAccountRoute($this->createStub(QueryFactory::class));

        $query = $route->route(GetRequest::from('/', []));

        $this->assertFalse($query);
    }
}
