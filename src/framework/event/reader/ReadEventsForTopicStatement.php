<?php declare(strict_types=1);
namespace example\framework\event;

use function array_map;
use function implode;
use function sprintf;
use SebastianBergmann\MysqliWrapper\ReadingDatabaseConnection;
use SebastianBergmann\MysqliWrapper\ReadStatement;

/**
 * @no-named-arguments
 */
final readonly class ReadEventsForTopicStatement implements ReadStatement
{
    /**
     * @var non-empty-list<non-empty-string>
     */
    private array $topics;

    /**
     * @param non-empty-list<non-empty-string> $topics
     */
    public function __construct(array $topics)
    {
        $this->topics = $topics;
    }

    /**
     * @return list<array{payload: non-empty-string}>
     */
    public function execute(ReadingDatabaseConnection $connection): array
    {
        /** @phpstan-ignore return.type */
        return $connection->query(
            sprintf(
                'SELECT payload
                   FROM event
                  WHERE topic IN (%s)
                  ORDER BY id;',
                implode(
                    ', ',
                    array_map(static fn (): string => '?', $this->topics),
                ),
            ),
            ...$this->topics,
        );
    }
}
