<?php declare(strict_types=1);
namespace example\framework\event;

use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
abstract readonly class Event
{
    private Uuid $id;
    private Uuid $correlationId;

    public function __construct(Uuid $id, Uuid $correlationId)
    {
        $this->id            = $id;
        $this->correlationId = $correlationId;
    }

    final public function id(): Uuid
    {
        return $this->id;
    }

    final public function correlationId(): Uuid
    {
        return $this->correlationId;
    }

    /**
     * @return non-empty-string
     */
    abstract public function topic(): string;

    /**
     * @return non-empty-string
     */
    abstract public function asString(): string;
}
