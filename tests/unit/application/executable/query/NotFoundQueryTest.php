<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundQuery::class)]
#[UsesClass(Response::class)]
#[Group('unit/application')]
#[Group('unit/application/query')]
#[Small]
#[TestDox('NotFoundQuery')]
final class NotFoundQueryTest extends TestCase
{
    #[TestDox('Returns "not found" response')]
    public function testReturnsNotFoundResponse(): void
    {
        $response = (new NotFoundQuery)->execute();

        $this->assertSame('not found', $response->body());
    }
}
