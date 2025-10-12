<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const DIRECTORY_SEPARATOR;
use const JSON_THROW_ON_ERROR;
use function array_column;
use function array_merge;
use function array_pop;
use function array_unique;
use function array_values;
use function assert;
use function exec;
use function explode;
use function file_put_contents;
use function in_array;
use function is_dir;
use function json_decode;
use function mkdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;
use PHPUnit\Event\Code\TestMethod;
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

    /**
     * @var array<class-string, array{uses: list<class-string>, emits: list<class-string>}>
     */
    private array $commands;

    /**
     * @var list<array{test: TestMethod, given: list<non-empty-string>, when: non-empty-string, then: list<non-empty-string>}>
     */
    private array $tests;

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

        $facade->registerSubscribers(
            new AdditionalInformationProvidedSubscriber($this),
            new TestRunnerFinishedSubscriber($this),
        );
    }

    public function testProvidedAdditionalInformation(AdditionalInformationProvided $event): void
    {
        /**
         * @var array{given: list<array{className: class-string, description: non-empty-string}>, when: array{className: class-string, description: non-empty-string}, then: list<array{className: class-string, description: non-empty-string}>} $data
         */
        $data = json_decode($event->additionalInformation(), true, flags: JSON_THROW_ON_ERROR);

        if (!isset($this->commands[$data['when']['className']])) {
            $this->commands[$data['when']['className']] = [
                'uses'  => [],
                'emits' => [],
            ];
        }

        $this->commands[$data['when']['className']]['uses'] = array_values(
            array_unique(
                array_merge(
                    $this->commands[$data['when']['className']]['uses'],
                    array_column($data['given'], 'className'),
                ),
            ),
        );

        $this->commands[$data['when']['className']]['emits'] = array_values(
            array_unique(
                array_merge(
                    $this->commands[$data['when']['className']]['emits'],
                    array_column($data['then'], 'className'),
                ),
            ),
        );

        $this->tests[] = [
            'test'  => $event->test(),
            'given' => array_column($data['given'], 'description'),
            'when'  => $data['when']['description'],
            'then'  => array_column($data['then'], 'description'),
        ];
    }

    public function testRunnerFinished(): void
    {
        $this->renderTests();
    }

    private function renderTests(): void
    {
        foreach ($this->tests as $test) {
            $tmp       = explode('\\', $test['test']->className());
            $className = array_pop($tmp);

            $dot = (new DotRenderer)->render(
                $test['given'],
                $test['when'],
                $test['then'],
            );

            $target = sprintf(
                '%s%s%s_%s.%s',
                $this->targetDirectory,
                DIRECTORY_SEPARATOR,
                $className,
                $test['test']->methodName(),
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
    }

    /**
     * @param non-empty-string $directory
     */
    private function createDirectory(string $directory): bool
    {
        return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
    }
}
