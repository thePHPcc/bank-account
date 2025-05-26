<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;
use function array_is_list;
use function array_pop;
use function assert;
use function explode;
use function file_put_contents;
use function is_array;
use function is_dir;
use function is_string;
use function json_decode;
use function mkdir;
use function sprintf;
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
        $data = json_decode($event->additionalInformation(), true, flags: JSON_THROW_ON_ERROR);

        assert(is_array($data));
        assert(isset($data['given']));
        assert(is_array($data['given']));
        assert(array_is_list($data['given']));
        assert(isset($data['when']));
        assert(is_string($data['when']));
        assert(isset($data['then']));
        assert(is_array($data['then']));
        assert(array_is_list($data['then']));

        $tmp       = explode('\\', $event->test()->className());
        $className = array_pop($tmp);

        file_put_contents(
            sprintf(
                '%s%s%s_%s.dot',
                $this->targetDirectory,
                DIRECTORY_SEPARATOR,
                $className,
                $event->test()->methodName(),
            ),
            (new DotRenderer)->render(
                /** @phpstan-ignore argument.type */
                $data['given'],
                /** @phpstan-ignore argument.type */
                $data['when'],
                /** @phpstan-ignore argument.type */
                $data['then'],
            ),
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
