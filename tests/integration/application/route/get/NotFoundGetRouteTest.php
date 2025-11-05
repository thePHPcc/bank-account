<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundGetRoute::class)]
#[Medium]
final class NotFoundGetRouteTest extends TestCase
{
    #[TestDox('Routes GET request to NotFoundQuery')]
    public function testRoutesGetRequestToNotFoundQuery(): void
    {
        $route = new NotFoundGetRoute;

        $query = $route->route(GetRequest::from('/', []));

        $this->assertInstanceOf(NotFoundQuery::class, $query);
    }
}
