<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\PreparationStarted as TestPreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber as PhpunitTestPreparationStartedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestPreparationStartedSubscriber extends Subscriber implements PhpunitTestPreparationStartedSubscriber
{
    public function notify(TestPreparationStarted $event): void
    {
        $this->extension()->testPreparationStarted($event);
    }
}
