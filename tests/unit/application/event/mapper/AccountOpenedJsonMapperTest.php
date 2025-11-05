<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\AccountOpenedEvent;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountOpenedJsonMapper::class)]
#[UsesClass(AccountOpenedEvent::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class AccountOpenedJsonMapperTest extends TestCase
{
    #[TestDox('Maps array to AccountOpenedEvent')]
    public function testMapsArrayToAccountOpenedEvent(): void
    {
        $eventId = Uuid::from('77db2dbe-309b-4e06-a3ec-ff36d7582f7c');
        $owner   = 'the-owner';

        $event = (new AccountOpenedJsonMapper)->fromArray(
            [
                'event_id' => $eventId->asString(),
                'owner'    => $owner,
            ],
        );

        $this->assertSame($eventId->asString(), $event->id()->asString());
        $this->assertSame($owner, $event->owner());
    }

    #[TestDox('Maps AccountOpenedEvent to array')]
    public function testMapsAccountOpenedEventToArray(): void
    {
        $owner = 'the-owner';

        $this->assertSame(
            [
                'owner' => $owner,
            ],
            (new AccountOpenedJsonMapper)->toArray(
                new AccountOpenedEvent(
                    Uuid::from('3ff5d994-977f-4e3c-afab-fe83db3e56a3'),
                    $owner,
                ),
            ),
        );
    }
}
