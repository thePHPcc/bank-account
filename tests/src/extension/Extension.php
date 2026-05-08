<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const JSON_THROW_ON_ERROR;
use function array_column;
use function array_merge;
use function array_unique;
use function array_values;
use function assert;
use function count;
use function explode;
use function in_array;
use function is_array;
use function json_decode;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\AdditionalInformationProvided;
use PHPUnit\Event\Test\Passed as TestPassed;
use PHPUnit\Event\Test\PreparationStarted as TestPreparationStarted;
use PHPUnit\Metadata\CoversClass;
use PHPUnit\Metadata\Group;
use PHPUnit\Metadata\TestDox;
use PHPUnit\Metadata\UsesClass;
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
     * @var array<class-string, list<class-string>>
     */
    private array $projectors = [];

    /**
     * @var list<array{test: TestMethod, given: list<non-empty-string>, when: non-empty-string, then: list<non-empty-string>}>
     */
    private array $tests = [];

    /**
     * @var array<class-string, array{title: non-empty-string, responsibilities: list<non-empty-string>, collaborators: list<class-string>}>
     */
    private array $crcCards = [];

    /**
     * @var ?class-string
     */
    private ?string $currentSut = null;

    /**
     * @var ?non-empty-string
     */
    private ?string $currentResponsibility = null;

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
            new TestPreparationStartedSubscriber($this),
            new TestPassedSubscriber($this),
            new TestStubCreatedSubscriber($this),
            new TestStubForIntersectionOfInterfacesCreatedSubscriber($this),
            new MockObjectCreatedSubscriber($this),
            new MockObjectForIntersectionOfInterfacesCreatedSubscriber($this),
            new TestRunnerFinishedSubscriber($this),
        );
    }

    public function testProvidedAdditionalInformation(AdditionalInformationProvided $event): void
    {
        $data = json_decode($event->additionalInformation(), true, flags: JSON_THROW_ON_ERROR);

        assert(is_array($data));

        if (isset($data['projector'])) {
            /**
             * @var array{projector: class-string, given: list<array{className: class-string, description: non-empty-string}>} $data
             */
            if (!isset($this->projectors[$data['projector']])) {
                $this->projectors[$data['projector']] = [];
            }

            $this->projectors[$data['projector']] = array_values(
                array_unique(
                    array_merge(
                        $this->projectors[$data['projector']],
                        array_column($data['given'], 'className'),
                    ),
                ),
            );

            return;
        }

        /**
         * @var array{given: list<array{className: class-string, description: non-empty-string}>, when: array{className: class-string, description: non-empty-string}, then: list<array{className: class-string, description: non-empty-string}>} $data
         */
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

    public function testPreparationStarted(TestPreparationStarted $event): void
    {
        $this->currentSut            = null;
        $this->currentResponsibility = null;

        $test = $event->test();

        if (!$test instanceof TestMethod) {
            return;
        }

        $classMetadata = $test->metadata()->isClassLevel();

        $optedIn = false;

        foreach ($classMetadata as $metadata) {
            if ($metadata instanceof Group && $metadata->groupName() === 'visual-documentation') {
                $optedIn = true;

                break;
            }
        }

        if (!$optedIn) {
            return;
        }

        $sut = null;

        foreach ($classMetadata as $metadata) {
            if ($metadata instanceof CoversClass) {
                $sut = $metadata->className();

                break;
            }
        }

        if ($sut === null) {
            return;
        }

        $title = self::shortName($sut);

        foreach ($classMetadata as $metadata) {
            if ($metadata instanceof TestDox) {
                $title = $metadata->text();

                break;
            }
        }

        if (!isset($this->crcCards[$sut])) {
            $this->crcCards[$sut] = [
                'title'            => $title,
                'responsibilities' => [],
                'collaborators'    => [],
            ];
        }

        foreach ($classMetadata as $metadata) {
            if ($metadata instanceof UsesClass) {
                $this->addCollaborator($sut, $metadata->className());
            }
        }

        $prettified = $test->testDox()->prettifiedMethodName();

        $this->currentSut            = $sut;
        $this->currentResponsibility = $prettified !== '' ? $prettified : null;
    }

    public function testPassed(TestPassed $event): void
    {
        if ($this->currentSut === null || $this->currentResponsibility === null) {
            return;
        }

        if (!isset($this->crcCards[$this->currentSut])) {
            return;
        }

        $card = $this->crcCards[$this->currentSut];

        if (!in_array($this->currentResponsibility, $card['responsibilities'], true)) {
            $card['responsibilities'][] = $this->currentResponsibility;
        }

        $this->crcCards[$this->currentSut] = $card;
    }

    /**
     * @param class-string $className
     */
    public function recordCollaborator(string $className): void
    {
        if ($this->currentSut === null) {
            return;
        }

        $this->addCollaborator($this->currentSut, $className);
    }

    public function testRunnerFinished(): void
    {
        $this->renderOverview();
        $this->renderGivenWhenThen();
        $this->renderCrcCards();
    }

    /**
     * @param class-string $sut
     * @param class-string $collaborator
     */
    private function addCollaborator(string $sut, string $collaborator): void
    {
        if ($collaborator === $sut) {
            return;
        }

        if (!isset($this->crcCards[$sut])) {
            return;
        }

        $card = $this->crcCards[$sut];

        if (in_array($collaborator, $card['collaborators'], true)) {
            return;
        }

        $card['collaborators'][] = $collaborator;

        $this->crcCards[$sut] = $card;
    }

    private function renderOverview(): void
    {
        new OverviewRenderer($this->targetDirectory, $this->format)->render(
            $this->commands,
            $this->projectors,
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

    private function renderCrcCards(): void
    {
        new CrcCardRenderer($this->targetDirectory, $this->format)->render(
            $this->crcCards,
        );
    }

    /**
     * @param class-string $fqcn
     *
     * @return non-empty-string
     */
    private static function shortName(string $fqcn): string
    {
        $parts = explode('\\', $fqcn);
        $short = $parts[count($parts) - 1];

        assert($short !== '');

        return $short;
    }
}
