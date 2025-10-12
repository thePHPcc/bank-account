<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use PHPUnit\Event\Test\AdditionalInformationProvided as TestProvidedAdditionalInformation;
use PHPUnit\Event\Test\AdditionalInformationProvidedSubscriber as TestProvidedAdditionalInformationSubscriber;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final readonly class AdditionalInformationProvidedSubscriber extends Subscriber implements TestProvidedAdditionalInformationSubscriber
{
    public function notify(TestProvidedAdditionalInformation $event): void
    {
        $this->extension()->testProvidedAdditionalInformation($event);
    }
}
