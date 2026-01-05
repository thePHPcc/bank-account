<?php declare(strict_types=1);
namespace example\framework\event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;

#[CoversClass(DatabaseEventWriter::class)]
#[Group('integration/framework')]
#[Group('integration/framework/event')]
#[Medium]
final class DatabaseEventWriterTest extends DatabaseTestCase
{
    public function testWritesEventToDatabase(): void
    {
        $this->emptyTable('event');

        $this->writer()->write($this->event());

        $this->assertTableEqualsCsvFile(
            __DIR__ . '/../../../expectation/event.csv',
            'event',
            $this->eventSchema(),
        );
    }
}
