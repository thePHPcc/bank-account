<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(CloseAccountCommand::class)]
#[Small]
final class CloseAccountCommandTest extends TestCase
{
    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('Close account', $this->command()->asString());
    }

    private function command(): CloseAccountCommand
    {
        return new CloseAccountCommand;
    }
}
