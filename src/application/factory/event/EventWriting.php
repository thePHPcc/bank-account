<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\event\DatabaseEventWriter;
use example\framework\event\EventWriter;
use example\framework\event\SubscribableEventDispatcher;
use example\framework\event\WritingEventSubscriber;
use example\framework\library\RandomUuidGenerator;
use SebastianBergmann\MysqliWrapper\MysqliWritingDatabaseConnection;

/**
 * @no-named-arguments
 *
 * @codeCoverageIgnore
 */
trait EventWriting
{
    use EventJsonMapper;

    public function createEventEmitter(): EventEmitter
    {
        return new DispatchingEventEmitter(
            new SubscribableEventDispatcher(
                new WritingEventSubscriber($this->createEventWriter()),
            ),
            new RandomUuidGenerator,
        );
    }

    private function createEventWriter(): EventWriter
    {
        return new DatabaseEventWriter(
            $this->createDatabaseConnectionForWritingEvents(),
            $this->createEventJsonMapper(),
        );
    }

    private function createDatabaseConnectionForWritingEvents(): MysqliWritingDatabaseConnection
    {
        return MysqliWritingDatabaseConnection::connect(
            '127.0.0.1',
            'event_writer',
            'event_writer_password',
            'bankaccount',
        );
    }
}
