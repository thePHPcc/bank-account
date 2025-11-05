<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAccountCommand::class)]
#[Small]
final class OpenAccountCommandTest extends TestCase
{
    private const string OWNER = 'the-owner';

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('Open account', $this->command()->asString());
    }

    public function testHasOwner(): void
    {
        $this->assertSame(self::OWNER, $this->command()->owner());
    }

    private function command(): OpenAccountCommand
    {
        return new OpenAccountCommand(
            self::OWNER,
        );
    }
}
