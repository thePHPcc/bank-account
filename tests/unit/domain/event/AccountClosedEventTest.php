<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountClosedEvent::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class AccountClosedEventTest extends TestCase
{
    private const string UUID = 'e307410c-e41b-4de9-bffb-bbfe8d94a279';

    public function testHasId(): void
    {
        $this->assertSame(self::UUID, $this->event()->id()->asString());
    }

    public function testHasTopic(): void
    {
        $this->assertSame('banking.account-closed', $this->event()->topic());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('Account closed', $this->event()->asString());
    }

    private function event(): AccountClosedEvent
    {
        return new AccountClosedEvent(
            new Uuid(self::UUID),
        );
    }
}
