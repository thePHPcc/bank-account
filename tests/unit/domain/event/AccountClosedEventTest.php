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
    private const string ID             = 'e307410c-e41b-4de9-bffb-bbfe8d94a279';
    private const string CORRELATION_ID = '9bf45927-5bdf-4e55-a8a5-c1e196141bac';

    public function testHasId(): void
    {
        $this->assertSame(self::ID, $this->event()->id()->asString());
    }

    public function testHasCorrelationId(): void
    {
        $this->assertSame(self::CORRELATION_ID, $this->event()->correlationId()->asString());
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
            new Uuid(self::ID),
            new Uuid(self::CORRELATION_ID),
        );
    }
}
