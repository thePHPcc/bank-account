<?php declare(strict_types=1);
namespace example\framework\event;

use function assert;
use example\framework\library\Uuid;
use SebastianBergmann\MysqliWrapper\ReadingDatabaseConnection;

/**
 * @no-named-arguments
 */
final readonly class DatabaseEventReader implements EventReader
{
    private ReadingDatabaseConnection $connection;
    private EventJsonMapper $mapper;

    public function __construct(ReadingDatabaseConnection $connection, EventJsonMapper $mapper)
    {
        $this->connection = $connection;
        $this->mapper     = $mapper;
    }

    public function correlationId(Uuid $correlationId): EventCollection
    {
        return $this->map(
            new ReadEventsForCorrelationStatement($correlationId->asString())->execute($this->connection),
        );
    }

    /**
     * @param non-empty-string ...$topics
     */
    public function topic(string ...$topics): EventCollection
    {
        assert($topics !== []);

        return $this->map(
            new ReadEventsForTopicStatement($topics)->execute($this->connection),
        );
    }

    /**
     * @param list<array{payload: non-empty-string}> $result
     */
    private function map(array $result): EventCollection
    {
        $events = [];

        foreach ($result as $row) {
            $events[] = $this->mapper->fromJson($row['payload']);
        }

        return EventCollection::fromArray($events);
    }
}
