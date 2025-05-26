<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;
use function array_is_list;
use function array_pop;
use function assert;
use function exec;
use function explode;
use function file_put_contents;
use function in_array;
use function is_array;
use function is_dir;
use function is_string;
use function json_decode;
use function mkdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
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

    /**
     * @var 'dot'|'pdf'|'png'|'svg'
     */
    private string $format;

    public function bootstrap(Configuration $configuration, ExtensionFacade $facade, ParameterCollection $parameters): void
    {
        $targetDirectory = '/tmp';

        if ($parameters->has('targetDirectory')) {
            $targetDirectory = $parameters->get('targetDirectory');
        }

        assert($targetDirectory !== '');

        $this->targetDirectory = $targetDirectory;

        $this->createDirectory($this->targetDirectory);

        $format = 'dot';

        if ($parameters->has('format')) {
            if (in_array($parameters->get('format'), ['dot', 'pdf', 'png', 'svg'], true)) {
                $format = $parameters->get('format');
            }
        }

        $this->format = $format;

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

        $dot = (new DotRenderer)->render(
            /** @phpstan-ignore argument.type */
            $data['given'],
            /** @phpstan-ignore argument.type */
            $data['when'],
            /** @phpstan-ignore argument.type */
            $data['then'],
        );

        $target = sprintf(
            '%s%s%s_%s.%s',
            $this->targetDirectory,
            DIRECTORY_SEPARATOR,
            $className,
            $event->test()->methodName(),
            $this->format,
        );

        if ($this->format === 'dot') {
            file_put_contents($target, $dot);

            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'graphviz');

        file_put_contents($tmpFile, $dot);

        exec(
            sprintf(
                'dot -T%s -o %s %s > /dev/null 2>&1',
                $this->format,
                $target,
                $tmpFile,
            ),
        );

        unlink($tmpFile);
    }

    /**
     * @param non-empty-string $directory
     */
    private function createDirectory(string $directory): bool
    {
        return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
    }
}
