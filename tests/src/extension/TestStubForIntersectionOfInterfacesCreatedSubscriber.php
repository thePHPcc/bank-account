<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\TestStubForIntersectionOfInterfacesCreated;
use PHPUnit\Event\Test\TestStubForIntersectionOfInterfacesCreatedSubscriber as PhpunitTestStubForIntersectionOfInterfacesCreatedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestStubForIntersectionOfInterfacesCreatedSubscriber extends Subscriber implements PhpunitTestStubForIntersectionOfInterfacesCreatedSubscriber
{
    public function notify(TestStubForIntersectionOfInterfacesCreated $event): void
    {
        foreach ($event->interfaces() as $interface) {
            $this->extension()->recordCollaborator($interface);
        }
    }
}
