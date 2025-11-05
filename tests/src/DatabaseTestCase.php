<?php declare(strict_types=1);
namespace example\framework\event;

use example\framework\library\Uuid;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CsvParser\FieldDefinition as CsvFieldDefinition;
use SebastianBergmann\CsvParser\Schema as CsvSchema;
use SebastianBergmann\CsvParser\Type as CsvFieldType;
use SebastianBergmann\MysqliWrapper\MysqliReadingDatabaseConnection;
use SebastianBergmann\MysqliWrapper\MysqliWritingDatabaseConnection;
use SebastianBergmann\MysqliWrapper\Testing\Testing;
use Throwable;

abstract class DatabaseTestCase extends TestCase
{
    use Testing;

    protected function connectionForReadingEvents(): MysqliReadingDatabaseConnection
    {
        try {
            return MysqliReadingDatabaseConnection::connect(
                '127.0.0.1',
                'event_reader',
                'event_reader_password',
                'bankaccount',
            );
        } catch (Throwable) {
            $this->markTestSkipped('Could not connect to test database');
        }
    }

    protected function connectionForWritingEvents(): MysqliWritingDatabaseConnection
    {
        try {
            return MysqliWritingDatabaseConnection::connect(
                '127.0.0.1',
                'event_writer',
                'event_writer_password',
                'bankaccount',
            );
        } catch (Throwable) {
            $this->markTestSkipped('Could not connect to test database');
        }
    }

    /**
     * @return array{host: non-empty-string, username: non-empty-string, password: non-empty-string, database: non-empty-string}
     */
    protected function configurationForTesting(): array
    {
        return [
            'host'     => '127.0.0.1',
            'username' => 'test_fixture_manager',
            'password' => 'test_fixture_manager_password',
            'database' => 'bankaccount',
        ];
    }

    protected function prepareEvent(): void
    {
        $this->emptyTable('event');

        $this->writer()->write($this->event());
    }

    protected function reader(): DatabaseEventReader
    {
        return new DatabaseEventReader($this->connectionForReadingEvents(), $this->mapper());
    }

    protected function writer(): DatabaseEventWriter
    {
        return new DatabaseEventWriter($this->connectionForWritingEvents(), $this->mapper());
    }

    protected function mapper(): EventJsonMapper
    {
        return new EventJsonMapper(['the-topic' => new DummyEventMapper]);
    }

    protected function event(): Event
    {
        return new DummyEvent(
            Uuid::from('b5578a2a-3188-470c-a2b7-3a249faed6fb'),
            'value',
        );
    }

    protected function eventSchema(): CsvSchema
    {
        return CsvSchema::from(
            CsvFieldDefinition::from(1, 'topic', CsvFieldType::string()),
            CsvFieldDefinition::from(2, 'event_id', CsvFieldType::string()),
            CsvFieldDefinition::from(3, 'payload', CsvFieldType::string()),
        );
    }
}
