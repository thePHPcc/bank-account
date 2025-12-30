<?php declare(strict_types=1);
namespace example\framework\event;

use example\framework\library\Uuid;

/**
 * @no-named-arguments
 */
interface EventReader
{
    public function correlationId(Uuid $correlationId): EventCollection;

    /**
     * @param non-empty-string ...$topics
     */
    public function topic(string ...$topics): EventCollection;
}
