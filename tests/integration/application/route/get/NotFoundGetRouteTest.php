<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundGetRoute::class)]
#[Group('integration/application')]
#[Group('integration/application/routing')]
#[Medium]
#[TestDox('NotFoundGetRoute')]
final class NotFoundGetRouteTest extends TestCase
{
    #[TestDox('Routes GET request not handled by other routes to NotFoundQuery')]
    public function testRoutesGetRequestToNotFoundQuery(): void
    {
        $route = new NotFoundGetRoute;

        $query = $route->route(GetRequest::from('/', []));

        $this->assertInstanceOf(NotFoundQuery::class, $query);
    }
}
