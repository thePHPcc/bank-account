<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\Passed as TestPassed;
use PHPUnit\Event\Test\PassedSubscriber as PhpunitTestPassedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestPassedSubscriber extends Subscriber implements PhpunitTestPassedSubscriber
{
    public function notify(TestPassed $event): void
    {
        $this->extension()->testPassed($event);
    }
}
