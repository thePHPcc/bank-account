<?php declare(strict_types=1);
namespace example\framework\event;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubscribableEventDispatcher::class)]
#[UsesClass(Event::class)]
#[UsesClass(Uuid::class)]
#[Group('framework')]
#[Group('framework/event')]
#[Small]
final class SubscribableEventDispatcherTest extends TestCase
{
    public function testDispatchesEventsToSubscribers(): void
    {
        $event      = $this->event();
        $subscriber = $this->createMock(EventSubscriber::class);
        $dispatcher = new SubscribableEventDispatcher($subscriber);

        $subscriber
            ->expects($this->once())
            ->method('notify')
            ->with($event);

        $dispatcher->dispatch($event);
    }

    private function event(): Event
    {
        return new DummyEvent(
            Uuid::from('9f0fd1e7-46b1-40cd-9665-1b7535e187c8'),
            Uuid::from('01168ef5-56b1-42a7-a2de-3fa6f8f7cce3'),
            'value',
        );
    }
}
