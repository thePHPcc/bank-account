<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\TestRunner\Finished as TestRunnerFinished;
use PHPUnit\Event\TestRunner\FinishedSubscriber as PhpunitTestRunnerFinishedSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class TestRunnerFinishedSubscriber extends Subscriber implements PhpunitTestRunnerFinishedSubscriber
{
    public function notify(TestRunnerFinished $event): void
    {
        $this->extension()->testRunnerFinished();
    }
}
