<?php declare(strict_types=1);
namespace example\caledonia\domain;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PriceChangedEvent::class)]
#[UsesClass(Price::class)]
#[UsesClass(Uuid::class)]
#[Group('domain')]
#[Small]
final class PriceChangedEventTest extends TestCase
{
    private const string UUID = '34bfb993-7044-4c1e-902b-f733ca4dded5';

    /**
     * @return non-empty-list<array{0: non-empty-string, 1: Price, 2: Price}>
     */
    public static function provider(): array
    {
        return [
            [
                'Price for bread increased from 1 to 2',
                Price::from(1),
                Price::from(2),
            ],
            [
                'Price for bread decreased from 2 to 1',
                Price::from(2),
                Price::from(1),
            ],
        ];
    }

    public function testHasId(): void
    {
        $this->assertSame(self::UUID, $this->event()->id()->asString());
    }

    public function testHasTopic(): void
    {
        $this->assertSame('market.price-changed', $this->event()->topic());
    }

    public function testHasGood(): void
    {
        $this->assertSame(Good::Bread, $this->event()->good());
    }

    public function testHasOldPrice(): void
    {
        $this->assertSame(1, $this->event()->old()->asInt());
    }

    public function testHasNewPrice(): void
    {
        $this->assertSame(2, $this->event()->new()->asInt());
    }

    #[DataProvider('provider')]
    public function testCanBeRepresentedAsString(string $expected, Price $old, Price $new): void
    {
        $event = new PriceChangedEvent(
            new Uuid(self::UUID),
            Good::Bread,
            $old,
            $new,
        );

        $this->assertSame($expected, $event->asString());
    }

    public function testOldAndNewPriceMustBeDifferent(): void
    {
        $this->expectException(OldAndNewPriceMustBeDifferentException::class);

        new PriceChangedEvent(
            new Uuid(self::UUID),
            Good::Bread,
            Price::from(1),
            Price::from(1),
        );
    }

    private function event(): PriceChangedEvent
    {
        return new PriceChangedEvent(
            new Uuid(self::UUID),
            Good::Bread,
            Price::from(1),
            Price::from(2),
        );
    }
}
