<?php declare(strict_types=1);
namespace example\caledonia\application;

use example\caledonia\domain\Good;
use example\caledonia\domain\Price;
use example\caledonia\domain\SellGoodCommand;
use example\framework\event\EventTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;

#[CoversClass(ProcessingSellGoodCommandProcessor::class)]
#[Medium]
#[TestDox('ProcessingPurchaseSellCommandProcessor')]
final class SellGoodCommandProcessorTest extends EventTestCase
{
    #[TestDox('A GoodSoldEvent and a PriceChangedEvent are emitted when the price changes after a good is sold')]
    public function testEmitsGoodPurchasedAndPriceChangedEvents(): void
    {
        $this->given();

        $this->when(new SellGoodCommand(Good::Bread, 1));

        $this->then(
            $this->goodSold(Good::Bread, Price::from(10), 1),
            $this->priceChanged(Good::Bread, Price::from(10), Price::from(9)),
        );
    }
}
