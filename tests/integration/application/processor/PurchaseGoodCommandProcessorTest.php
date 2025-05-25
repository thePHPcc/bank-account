<?php declare(strict_types=1);
namespace example\caledonia\application;

use example\caledonia\domain\Good;
use example\caledonia\domain\Price;
use example\caledonia\domain\PurchaseGoodCommand;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingPurchaseGoodCommandProcessor::class)]
#[Medium]
#[TestDox('ProcessingPurchaseGoodCommandProcessor')]
final class PurchaseGoodCommandProcessorTest extends EventTestCase
{
    #[TestDox('A GoodPurchasedEvent and a PriceChangedEvent are emitted when the price changes after a good is purchased')]
    public function testEmitsGoodPurchasedAndPriceChangedEvents(): void
    {
        $this->given(
            $this->goodPurchased(Good::Bread, Price::from(10), 1),
            $this->goodPurchased(Good::Bread, Price::from(11), 1),
        );

        $this->when(new PurchaseGoodCommand(Good::Bread, 1));

        $this->then(
            $this->goodPurchased(Good::Bread, Price::from(11), 1),
            $this->priceChanged(Good::Bread, Price::from(11), Price::from(12)),
        );
    }
}
