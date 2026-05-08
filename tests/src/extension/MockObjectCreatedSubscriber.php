<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\MockObjectCreated;
use PHPUnit\Event\Test\MockObjectCreatedSubscriber as PhpunitMockObjectCreatedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MockObjectCreatedSubscriber extends Subscriber implements PhpunitMockObjectCreatedSubscriber
{
    public function notify(MockObjectCreated $event): void
    {
        $this->extension()->recordCollaborator($event->className());
    }
}
