<?php declare(strict_types=1);
namespace example\framework\event;

use const PHP_EOL;
use function array_keys;
use function array_values;
use function assert;
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
    private EventReader&Stub $reader;
    private MarketEventSourcer $sourcer;
    private DispatchingEventEmitter $emitter;
    private CollectingEventDispatcher $dispatcher;
    private string $documentation;

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

        $this->documentation = 'Given:' . PHP_EOL . PHP_EOL;

        foreach ($events as $event) {
            $this->documentation .= '    - ' . $event->asString() . PHP_EOL;
        }

        $this->documentation .= PHP_EOL;
    }

    final protected function when(PurchaseGoodCommand|SellGoodCommand $command): void
    {
        $processor = $this->processorFor($command);

        $processor->process($command);

        $this->documentation .= 'When:' . PHP_EOL . PHP_EOL;
        $this->documentation .= '    - ' . $command->asString() . PHP_EOL . PHP_EOL;
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

        $this->documentation .= 'Then:' . PHP_EOL . PHP_EOL;

        foreach ($events as $event) {
            $this->documentation .= '    - ' . $event->asString() . PHP_EOL;
        }

        $this->provideAdditionalInformation($this->documentation);
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
}
