<?php declare(strict_types=1);
namespace example\framework\event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\MysqliWrapper\ReadingDatabaseConnection;

#[CoversClass(ReadEventsStatement::class)]
#[Group('framework')]
#[Group('framework/event')]
#[Small]
final class ReadEventsStatementTest extends TestCase
{
    public function testExecutesStatementToReadEventsByTopic(): void
    {
        $connection = $this->createMock(ReadingDatabaseConnection::class);

        $connection
            ->expects($this->once())
            ->method('query')
            ->with(
                'SELECT payload
                   FROM event
                  WHERE topic IN (?)
                  ORDER BY id;',
                'the-topic',
            );

        $statement = new ReadEventsStatement(['the-topic']);

        $statement->execute($connection);
    }

    public function testExecutesStatementToReadEventsByTopics(): void
    {
        $connection = $this->createMock(ReadingDatabaseConnection::class);

        $connection
            ->expects($this->once())
            ->method('query')
            ->with(
                'SELECT payload
                   FROM event
                  WHERE topic IN (?, ?)
                  ORDER BY id;',
                'the-topic',
                'another-topic',
            );

        $statement = new ReadEventsStatement(['the-topic', 'another-topic']);

        $statement->execute($connection);
    }
}
