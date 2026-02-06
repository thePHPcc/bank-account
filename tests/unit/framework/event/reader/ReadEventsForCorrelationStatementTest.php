<?php declare(strict_types=1);
namespace example\framework\event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\MysqliWrapper\ReadingDatabaseConnection;

#[CoversClass(ReadEventsForCorrelationStatement::class)]
#[Group('unit/framework')]
#[Group('unit/framework/event')]
#[Small]
final class ReadEventsForCorrelationStatementTest extends TestCase
{
    public function testExecutesStatementToReadEventsForAccount(): void
    {
        $connection = $this->createMock(ReadingDatabaseConnection::class);

        $connection
            ->expects($this->once())
            ->method('query')
            ->with(
                'SELECT payload
               FROM event
              WHERE correlation_id = ?
              ORDER BY id;',
                'correlation-id',
            )
            ->seal();

        $statement = new ReadEventsForCorrelationStatement('correlation-id');

        $statement->execute($connection);
    }
}
