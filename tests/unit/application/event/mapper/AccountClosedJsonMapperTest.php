<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\bankaccount\domain\AccountClosedEvent;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountClosedJsonMapper::class)]
#[UsesClass(AccountClosedEvent::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/application')]
#[Group('unit/application/event')]
#[Small]
#[TestDox('AccountClosedJsonMapper')]
final class AccountClosedJsonMapperTest extends TestCase
{
    #[TestDox('Maps array to AccountClosedEvent')]
    public function testMapsArrayToAccountClosedEvent(): void
    {
        $eventId       = Uuid::from('e64f6762-fe1b-4d10-8288-5bd309235348');
        $correlationId = Uuid::from('5f7f006d-f7d7-43a3-aef5-6ce7223525b3');

        $event = (new AccountClosedJsonMapper)->fromArray(
            [
                'event_id'       => $eventId->asString(),
                'correlation_id' => $correlationId->asString(),
            ],
        );

        $this->assertSame($eventId->asString(), $event->id()->asString());
    }

    #[TestDox('Maps AccountClosedEvent to array')]
    public function testMapsAccountClosedEventToArray(): void
    {
        $this->assertSame(
            [
            ],
            (new AccountClosedJsonMapper)->toArray(
                new AccountClosedEvent(
                    Uuid::from('ec41052a-6aed-4e36-967d-936ac1c6bbda'),
                    Uuid::from('748453c0-dd22-4aed-b555-bf4b6562bd98'),
                ),
            ),
        );
    }
}
