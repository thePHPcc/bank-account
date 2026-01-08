<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const JSON_THROW_ON_ERROR;
use function array_column;
use function array_merge;
use function array_unique;
use function array_values;
use function assert;
use function in_array;
use function json_decode;
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
     * @var array<class-string, array{consumes: list<class-string>, emits: list<class-string>}>
     */
    private array $commands = [];

    /**
     * @var list<array{test: TestMethod, given: list<non-empty-string>, when: non-empty-string, then: list<non-empty-string>}>
     */
    private array $tests = [];

    public function bootstrap(Configuration $configuration, ExtensionFacade $facade, ParameterCollection $parameters): void
    {
        $targetDirectory = '/tmp';

        if ($parameters->has('targetDirectory')) {
            $targetDirectory = $parameters->get('targetDirectory');
        }

        assert($targetDirectory !== '');

        $this->targetDirectory = $targetDirectory;

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
                'consumes' => [],
                'emits'    => [],
            ];
        }

        $this->commands[$data['when']['className']]['consumes'] = array_values(
            array_unique(
                array_merge(
                    $this->commands[$data['when']['className']]['consumes'],
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
        $this->renderOverview();
        $this->renderGivenWhenThen();
    }

    private function renderOverview(): void
    {
        new OverviewRenderer($this->targetDirectory, $this->format)->render(
            $this->commands,
        );
    }

    private function renderGivenWhenThen(): void
    {
        foreach ($this->tests as $test) {
            new GivenWhenThenRenderer($this->targetDirectory, $this->format)->render(
                $test['test'],
                $test['given'],
                $test['when'],
                $test['then'],
            );
        }
    }
}
