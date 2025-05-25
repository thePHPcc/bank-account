<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const DIRECTORY_SEPARATOR;
use function assert;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;
use function str_replace;
use PHPUnit\Event\Test\AdditionalInformationProvided;
use PHPUnit\Runner\Extension\Extension as ExtensionInterface;
use PHPUnit\Runner\Extension\Facade as ExtensionFacade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class Extension implements ExtensionInterface
{
    /**
     * @var non-empty-string
     */
    private string $targetDirectory;

    public function bootstrap(Configuration $configuration, ExtensionFacade $facade, ParameterCollection $parameters): void
    {
        $targetDirectory = '/tmp';

        if ($parameters->has('targetDirectory')) {
            $targetDirectory = $parameters->get('targetDirectory');
        }

        assert($targetDirectory !== '');

        $this->targetDirectory = $targetDirectory;

        $this->createDirectory($this->targetDirectory);

        $facade->registerSubscriber(new AdditionalInformationProvidedSubscriber($this));
    }

    public function testProvidedAdditionalInformation(AdditionalInformationProvided $event): void
    {
        file_put_contents(
            sprintf(
                '%s%s%s.dot',
                $this->targetDirectory,
                DIRECTORY_SEPARATOR,
                str_replace(['\\', '::'], '_', $event->test()->id()),
            ),
            $event->additionalInformation(),
        );
    }

    /**
     * @param non-empty-string $directory
     */
    private function createDirectory(string $directory): bool
    {
        return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
    }
}
