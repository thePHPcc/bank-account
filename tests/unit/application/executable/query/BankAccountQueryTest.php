<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\Response;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BankAccountQuery::class)]
#[UsesClass(Response::class)]
#[UsesClass(Uuid::class)]
#[Small]
#[TestDox('BankAccountQuery')]
final class BankAccountQueryTest extends TestCase
{
    #[TestDox('Returns response with projected HTML')]
    public function testReturnsResponseWithProjectedHtml(): void
    {
        $accountId = Uuid::from('81e61f38-5c81-490b-86cd-09e1b418ad61');

        $html = 'html';

        $reader = $this->createStub(BankAccountProjectionReader::class);

        $reader
            ->method('read')
            ->willReturn($html);

        $response = new BankAccountQuery($reader, $accountId)->execute();

        $this->assertSame($html, $response->body());
    }
}
