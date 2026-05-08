<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\TestStubCreated;
use PHPUnit\Event\Test\TestStubCreatedSubscriber as PhpunitTestStubCreatedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestStubCreatedSubscriber extends Subscriber implements PhpunitTestStubCreatedSubscriber
{
    public function notify(TestStubCreated $event): void
    {
        $this->extension()->recordCollaborator($event->className());
    }
}
