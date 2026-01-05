<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountCommand::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/domain')]
#[Group('unit/domain/command')]
#[Small]
final class CloseAccountCommandTest extends TestCase
{
    private const string ACCOUNT_ID = '6eb2571c-d061-492b-9008-e10a31de1828';

    public function testHasAccountId(): void
    {
        $this->assertSame(self::ACCOUNT_ID, $this->command()->accountId()->asString());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('Close account', $this->command()->asString());
    }

    private function command(): CloseAccountCommand
    {
        return new CloseAccountCommand(
            Uuid::from(self::ACCOUNT_ID),
        );
    }
}
