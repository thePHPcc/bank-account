<?php declare(strict_types=1);
namespace example\framework\event;

use SebastianBergmann\MysqliWrapper\ReadingDatabaseConnection;
use SebastianBergmann\MysqliWrapper\ReadStatement;

/**
 * @no-named-arguments
 */
final readonly class ReadEventsForCorrelationStatement implements ReadStatement
{
    private string $correlationId;

    public function __construct(string $correlationId)
    {
        $this->correlationId = $correlationId;
    }

    /**
     * @return list<array{payload: non-empty-string}>
     */
    public function execute(ReadingDatabaseConnection $connection): array
    {
        /** @phpstan-ignore return.type */
        return $connection->query(
            'SELECT payload
               FROM event
              WHERE correlation_id = ?
              ORDER BY id;',
            $this->correlationId,
        );
    }
}
