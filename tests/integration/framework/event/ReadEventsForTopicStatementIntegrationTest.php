<?php declare(strict_types=1);
namespace example\framework\event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;

#[CoversClass(ReadEventsForTopicStatement::class)]
#[Group('integration/framework')]
#[Group('integration/framework/event')]
#[Medium]
final class ReadEventsForTopicStatementIntegrationTest extends DatabaseTestCase
{
    public function testReadsEventsFromDatabase(): void
    {
        $this->prepareEvent();

        $statement = new ReadEventsForTopicStatement(['the-topic']);

        $result = $statement->execute($this->connectionForReadingEvents());

        $this->assertSame(
            [
                0 => [
                    'payload' => '{"topic":"the-topic","event_id":"b5578a2a-3188-470c-a2b7-3a249faed6fb","correlation_id":"33621c80-0023-425c-a43e-7eccb239d6d4","key":"value"}',
                ],
            ],
            $result,
        );
    }
}
