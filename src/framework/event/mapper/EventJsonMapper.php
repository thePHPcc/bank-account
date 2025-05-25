<?php declare(strict_types=1);
namespace example\framework\event;

use const JSON_THROW_ON_ERROR;
use function array_key_exists;
use function array_merge;
use function assert;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use JsonException;

/**
 * @no-named-arguments
 */
final class EventJsonMapper
{
    /**
     * @var array<string, EventArrayMapper>
     */
    private array $mappers;

    /**
     * @param array<string, EventArrayMapper> $mappers
     */
    public function __construct(array $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * @param non-empty-string $json
     *
     * @throws CannotMapEventException
     */
    public function fromJson(string $json): Event
    {
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);

        assert(is_array($data));

        $this->ensureJsonCanBeMapped($data);

        assert(isset($data['topic']));
        assert(is_string($data['topic']));
        assert(isset($data['event_id']));
        assert(is_string($data['event_id']));
        assert(isset($this->mappers[$data['topic']]));

        /** @phpstan-ignore argument.type */
        return $this->mappers[$data['topic']]->fromArray($data);
    }

    /**
     * @throws CannotMapEventException
     *
     * @return non-empty-string
     */
    public function toJson(Event $event): string
    {
        $this->ensureEventCanBeMapped($event->topic());

        assert(isset($this->mappers[$event->topic()]));

        $metadata = [
            'topic'    => $event->topic(),
            'event_id' => $event->id()->asString(),
        ];

        $data = array_merge(
            $metadata,
            $this->mappers[$event->topic()]->toArray($event),
        );

        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
            // @codeCoverageIgnoreStart
        } catch (JsonException $e) {
            throw new CannotMapEventException(
                $e->getMessage(),
                $e->getCode(),
                $e,
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param non-empty-string $topic
     *
     * @throws CannotMapEventException
     */
    private function ensureEventCanBeMapped(string $topic): void
    {
        if (!isset($this->mappers[$topic])) {
            throw new CannotMapEventException;
        }
    }

    /**
     * @param array<mixed> $data
     *
     * @throws CannotMapEventException
     */
    private function ensureJsonCanBeMapped(array $data): void
    {
        if (!array_key_exists('topic', $data) ||
            !is_string($data['topic']) ||
            !array_key_exists('event_id', $data) ||
            !is_string($data['event_id'])) {
            throw new CannotMapEventException;
        }

        if (!isset($this->mappers[$data['topic']])) {
            throw new CannotMapEventException;
        }
    }
}
