<?php declare(strict_types=1);
namespace example\caledonia\domain;

use function sprintf;
use example\framework\event\Event;
use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
final readonly class PriceChangedEvent extends Event
{
    private Good $good;
    private Price $old;
    private Price $new;

    /**
     * @throws OldAndNewPriceMustBeDifferentException
     */
    public function __construct(Uuid $id, Good $good, Price $old, Price $new)
    {
        $this->ensureOldAndNewPriceAreDifferent($old, $new);

        parent::__construct($id);

        $this->good = $good;
        $this->old  = $old;
        $this->new  = $new;
    }

    /**
     * @return non-empty-string
     */
    public function topic(): string
    {
        return 'market.price-changed';
    }

    /**
     * @return non-empty-string
     */
    public function asString(): string
    {
        return sprintf(
            'Price for %s %s from %d to %d',
            $this->good->value,
            $this->old->asInt() < $this->new->asInt() ? 'increased' : 'decreased',
            $this->old->asInt(),
            $this->new->asInt(),
        );
    }

    public function good(): Good
    {
        return $this->good;
    }

    public function old(): Price
    {
        return $this->old;
    }

    public function new(): Price
    {
        return $this->new;
    }

    /**
     * @throws OldAndNewPriceMustBeDifferentException
     */
    private function ensureOldAndNewPriceAreDifferent(Price $old, Price $new): void
    {
        if ($new->equals($old)) {
            throw new OldAndNewPriceMustBeDifferentException;
        }
    }
}
