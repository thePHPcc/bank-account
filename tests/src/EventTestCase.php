<?php declare(strict_types=1);
namespace example\framework\event;

use const PHP_EOL;
use function array_keys;
use function array_values;
use function assert;
use function implode;
use function sprintf;
use function str_replace;
use example\caledonia\application\DispatchingEventEmitter;
use example\caledonia\application\MarketEventSourcer;
use example\caledonia\application\ProcessingPurchaseGoodCommandProcessor;
use example\caledonia\application\ProcessingSellGoodCommandProcessor;
use example\caledonia\application\PurchaseGoodCommandProcessor;
use example\caledonia\application\SellGoodCommandProcessor;
use example\caledonia\domain\Good;
use example\caledonia\domain\GoodPurchasedEvent;
use example\caledonia\domain\GoodSoldEvent;
use example\caledonia\domain\Price;
use example\caledonia\domain\PriceChangedEvent;
use example\caledonia\domain\PurchaseGoodCommand;
use example\caledonia\domain\SellGoodCommand;
use example\framework\library\RandomUuidGenerator;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

abstract class EventTestCase extends TestCase
{
    private const string DOT_TEMPLATE = <<<'EOT'
strict digraph G {
    graph [labelloc="t", label="", splines=true, overlap=false, rankdir="LR"];
    node [shape=box, width=3, height=3, fixedsize=true, style="filled", fontname="Balsamiq Sans", fontsize=18, penwidth=2];
    ratio = auto;

    subgraph cluster_given {
        label="Given these events were emitted in the past";
        style=filled;
        color=lightgrey;
        fontname="Balsamiq Sans";
        fontsize=18;
{{{given}}}
    }

    subgraph cluster_when {
        label="When this command is processed";
        style=filled;
        color=lightgrey;
        fontname="Balsamiq Sans";
        fontsize=18;

        command [label="{{{when}}}", fillcolor="#729fcf",color="#3465a4"];
    }

    subgraph cluster_then {
        label="Then these events are emitted";
        style=filled;
        color=lightgrey;
        fontname="Balsamiq Sans";
        fontsize=18;
{{{then}}}
    }

    {{{edges}}} [style=invis];
}

EOT;
    private EventReader&Stub $reader;
    private MarketEventSourcer $sourcer;
    private DispatchingEventEmitter $emitter;
    private CollectingEventDispatcher $dispatcher;

    /**
     * @var list<non-empty-string>
     */
    private array $given = [];
    private string $when;

    /**
     * @var list<non-empty-string>
     */
    private array $then = [];

    final protected function setUp(): void
    {
        $this->reader     = $this->createStub(EventReader::class);
        $this->sourcer    = new MarketEventSourcer($this->reader);
        $this->dispatcher = new CollectingEventDispatcher;

        $this->emitter = new DispatchingEventEmitter(
            $this->dispatcher,
            new RandomUuidGenerator,
        );
    }

    final protected function given(Event ...$events): void
    {
        $events = EventCollection::fromArray(array_values($events));

        $this
            ->reader
            ->method('topic')
            ->willReturn($events);

        foreach ($events as $event) {
            $this->given[] = $event->asString();
        }
    }

    final protected function when(PurchaseGoodCommand|SellGoodCommand $command): void
    {
        $processor = $this->processorFor($command);

        $processor->process($command);

        $this->when = $command->asString();
    }

    final protected function then(Event ...$events): void
    {
        $events = EventCollection::fromArray(array_values($events));

        $expected = $events->asArray();
        $actual   = $this->dispatcher->events()->asArray();

        $this->assertSameSize($expected, $actual);

        foreach (array_keys($expected) as $key) {
            assert(isset($expected[$key]));
            assert(isset($actual[$key]));

            $this->assertEventObjectsAreEqualExceptForUuid($expected[$key], $actual[$key]);
        }

        foreach ($events as $event) {
            $this->then[] = $event->asString();
        }

        $this->provideMarkdown();
        $this->provideGraphViz();
    }

    /**
     * @param positive-int $amount
     */
    final protected function goodPurchased(Good $good, Price $price, int $amount): GoodPurchasedEvent
    {
        return new GoodPurchasedEvent(
            (new RandomUuidGenerator)->generate(),
            $good,
            $price,
            $amount,
        );
    }

    /**
     * @param positive-int $amount
     */
    final protected function goodSold(Good $good, Price $price, int $amount): GoodSoldEvent
    {
        return new GoodSoldEvent(
            (new RandomUuidGenerator)->generate(),
            $good,
            $price,
            $amount,
        );
    }

    final protected function priceChanged(Good $good, Price $old, Price $new): PriceChangedEvent
    {
        return new PriceChangedEvent(
            (new RandomUuidGenerator)->generate(),
            $good,
            $old,
            $new,
        );
    }

    private function processorFor(PurchaseGoodCommand|SellGoodCommand $command): PurchaseGoodCommandProcessor|SellGoodCommandProcessor
    {
        return match ($command::class) {
            PurchaseGoodCommand::class => new ProcessingPurchaseGoodCommandProcessor($this->emitter, $this->sourcer),
            SellGoodCommand::class     => new ProcessingSellGoodCommandProcessor($this->emitter, $this->sourcer),
        };
    }

    private function assertEventObjectsAreEqualExceptForUuid(Event $expected, Event $actual): void
    {
        $this->assertInstanceOf($expected::class, $actual);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            (array) $expected,
            (array) $actual,
            ["\0example\\framework\\event\\Event\0id"],
        );
    }

    private function provideMarkdown(): void
    {
        $buffer = 'Given:' . PHP_EOL . PHP_EOL;

        foreach ($this->given as $given) {
            $buffer .= '    - ' . $given . PHP_EOL;
        }

        $buffer .= PHP_EOL;

        $buffer .= 'When:' . PHP_EOL . PHP_EOL;
        $buffer .= '    - ' . $this->when . PHP_EOL . PHP_EOL;

        $buffer .= 'Then:' . PHP_EOL . PHP_EOL;

        foreach ($this->then as $then) {
            $buffer .= '    - ' . $then . PHP_EOL;
        }

        $this->provideAdditionalInformation($buffer);
    }

    private function provideGraphViz(): void
    {
        $edges = [];
        $given = '';

        foreach ($this->given as $index => $label) {
            $given .= sprintf(
                <<<'EOT'

        given_%d [label="%s", fillcolor="#fcaf3e",color="#f57900"];
EOT,
                $index + 1,
                $this->formatLabel($label),
            );

            $edges[] = sprintf('given_%d', $index + 1);
        }

        $edges[] = 'command';
        $then    = '';

        foreach ($this->then as $index => $label) {
            $then .= sprintf(
                <<<'EOT'

        then_%d [label="%s", fillcolor="#fcaf3e",color="#f57900"];
EOT,
                $index + 1,
                $this->formatLabel($label),
            );

            $edges[] = sprintf('then_%d', $index + 1);
        }

        $this->provideAdditionalInformation(
            /** @phpstan-ignore argument.type */
            str_replace(
                [
                    '{{{given}}}',
                    '{{{when}}}',
                    '{{{then}}}',
                    '{{{edges}}}',
                ],
                [
                    $given,
                    $this->when,
                    $then,
                    implode(' -> ', $edges),
                ],
                self::DOT_TEMPLATE,
            ),
        );
    }

    /**
     * @param non-empty-string $label
     *
     * @return non-empty-string
     */
    private function formatLabel(string $label): string
    {
        return str_replace(
            ['at price', 'from'],
            ['\nat price', '\nfrom'],
            $label,
        );
    }
}
