<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\MockObjectForIntersectionOfInterfacesCreated;
use PHPUnit\Event\Test\MockObjectForIntersectionOfInterfacesCreatedSubscriber as PhpunitMockObjectForIntersectionOfInterfacesCreatedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class MockObjectForIntersectionOfInterfacesCreatedSubscriber extends Subscriber implements PhpunitMockObjectForIntersectionOfInterfacesCreatedSubscriber
{
    public function notify(MockObjectForIntersectionOfInterfacesCreated $event): void
    {
        foreach ($event->interfaces() as $interface) {
            $this->extension()->recordCollaborator($interface);
        }
    }
}
