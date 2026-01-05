<?php declare(strict_types=1);
namespace example\framework\event;

use function assert;
use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;

#[CoversClass(DatabaseEventReader::class)]
#[Group('integration/framework')]
#[Group('integration/framework/event')]
#[Medium]
final class DatabaseEventReaderTest extends DatabaseTestCase
{
    public function testReadsEventsByCorrelation(): void
    {
        $this->prepareEvent();

        $events = $this->reader()->correlationId(
            Uuid::from('33621c80-0023-425c-a43e-7eccb239d6d4'),
        );

        $this->assertCount(1, $events);

        /** @phpstan-ignore offsetAccess.notFound */
        $event = $events->asArray()[0];

        assert($event instanceof DummyEvent);

        $this->assertSame('the-topic', $event->topic());
        $this->assertSame('b5578a2a-3188-470c-a2b7-3a249faed6fb', $event->id()->asString());
        $this->assertSame('33621c80-0023-425c-a43e-7eccb239d6d4', $event->correlationId()->asString());
        $this->assertSame('value', $event->key());
    }

    public function testReadsEventsByTopic(): void
    {
        $this->prepareEvent();

        $events = $this->reader()->topic('the-topic');

        $this->assertCount(1, $events);

        /** @phpstan-ignore offsetAccess.notFound */
        $event = $events->asArray()[0];

        assert($event instanceof DummyEvent);

        $this->assertSame('the-topic', $event->topic());
        $this->assertSame('b5578a2a-3188-470c-a2b7-3a249faed6fb', $event->id()->asString());
        $this->assertSame('33621c80-0023-425c-a43e-7eccb239d6d4', $event->correlationId()->asString());
        $this->assertSame('value', $event->key());
    }
}
