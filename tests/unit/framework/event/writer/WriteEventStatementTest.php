<?php declare(strict_types=1);
namespace example\framework\event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\MysqliWrapper\WritingDatabaseConnection;

#[CoversClass(WriteEventStatement::class)]
#[Group('framework')]
#[Group('framework/event')]
#[Small]
final class WriteEventStatementTest extends TestCase
{
    public function testExecutesStatementToWriteEvent(): void
    {
        $connection = $this->createMock(WritingDatabaseConnection::class);

        $connection
            ->expects($this->once())
            ->method('execute')
            ->with(
                'INSERT INTO event
                         (event_id, correlation_id, topic, payload)
                  VALUES (?, ?, ?, ?);',
                'id',
                'correlation-id',
                'topic',
                'payload',
            );

        $statement = new WriteEventStatement('id', 'correlation-id', 'topic', 'payload');

        $statement->execute($connection);
    }
}
