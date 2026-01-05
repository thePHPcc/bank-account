<?php declare(strict_types=1);
namespace example\framework\event;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Event::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/framework')]
#[Group('unit/framework/event')]
#[Small]
final class EventTest extends TestCase
{
    public function testHasTopic(): void
    {
        $this->assertSame('the-topic', $this->event()->topic());
    }

    public function testHasId(): void
    {
        $this->assertSame('9f0fd1e7-46b1-40cd-9665-1b7535e187c8', $this->event()->id()->asString());
    }

    public function testHasCorrelationId(): void
    {
        $this->assertSame('5778b38b-f8bf-4d4e-8ccc-b7253ecffc20', $this->event()->correlationId()->asString());
    }

    private function event(): Event
    {
        return new DummyEvent(
            Uuid::from('9f0fd1e7-46b1-40cd-9665-1b7535e187c8'),
            Uuid::from('5778b38b-f8bf-4d4e-8ccc-b7253ecffc20'),
            'value',
        );
    }
}
