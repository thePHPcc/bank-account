<?php declare(strict_types=1);
namespace example\bankaccount\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountOpenedEvent::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class AccountOpenedEventTest extends TestCase
{
    private const string UUID  = '9e9c67c5-8fd7-4561-9a06-97fbffce829c';
    private const string OWNER = 'the-owner';

    public function testHasId(): void
    {
        $this->assertSame(self::UUID, $this->event()->id()->asString());
    }

    public function testHasTopic(): void
    {
        $this->assertSame('banking.account-opened', $this->event()->topic());
    }

    public function testCanBeRepresentedAsString(): void
    {
        $this->assertSame('Account opened', $this->event()->asString());
    }

    public function testHasOwner(): void
    {
        $this->assertSame(self::OWNER, $this->event()->owner());
    }

    private function event(): AccountOpenedEvent
    {
        return new AccountOpenedEvent(
            new Uuid(self::UUID),
            self::OWNER,
        );
    }
}
