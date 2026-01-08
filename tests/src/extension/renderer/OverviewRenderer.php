<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const PHP_EOL;
use function array_keys;
use function array_last;
use function array_map;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function file_get_contents;
use function implode;
use function is_string;
use function rtrim;
use function sprintf;
use function str_replace;

final readonly class OverviewRenderer extends Renderer
{
    /**
     * @param array<class-string, array{consumes: list<class-string>, emits: list<class-string>}> $commands
     * @param array<class-string, list<class-string>>                                             $projectors
     */
    public function render(array $commands, array $projectors): void
    {
        $commandProcessors     = [];
        $commandProcessorNodes = '';
        $events                = [];
        $eventNodes            = '';
        $projectorNodes        = '';
        $emissionEdges         = '';
        $consumptionEdges      = '';

        foreach ($commands as $command => $_events) {
            $commandProcessor    = array_last(explode('\\', $command)) . 'Processor';
            $commandProcessors[] = $commandProcessor;

            $commandProcessorNodes .= sprintf(
                '  %s [label="%s"];' . PHP_EOL,
                $commandProcessor,
                $commandProcessor,
            );

            foreach ($_events['emits'] as $event) {
                $emissionEdges .= sprintf(
                    '  %s -> %s;' . PHP_EOL,
                    $commandProcessor,
                    array_last(explode('\\', $event)),
                );
            }

            foreach ($_events['consumes'] as $event) {
                $consumptionEdges .= sprintf(
                    '  %s -> %s;' . PHP_EOL,
                    array_last(explode('\\', $event)),
                    $commandProcessor,
                );
            }

            $events = array_merge(
                $events,
                $_events['consumes'],
                $_events['emits'],
            );
        }

        $events = array_map(
            static function (string $event): string
            {
                return array_last(explode('\\', $event));
            },
            array_unique($events),
        );

        foreach ($events as $event) {
            $eventNodes .= sprintf(
                '  %s [label="%s"];' . PHP_EOL,
                $event,
                $event,
            );
        }

        foreach ($projectors as $projector => $eventsConsumedByProjector) {
            foreach ($eventsConsumedByProjector as $event) {
                $consumptionEdges .= sprintf(
                    '  %s -> %s;' . PHP_EOL,
                    array_last(explode('\\', $event)),
                    array_last(explode('\\', $projector)),
                );
            }
        }

        $projectors = array_map(
            static function (string $projector): string
            {
                return array_last(explode('\\', $projector));
            },
            array_keys($projectors),
        );

        foreach ($projectors as $projector) {
            $projectorNodes .= sprintf(
                '  %s [label="%s"];' . PHP_EOL,
                $projector,
                $projector,
            );
        }

        $template = file_get_contents(__DIR__ . '/../templates/overview.dot');

        assert(is_string($template));

        $dot = str_replace(
            [
                '{{command_processor_nodes}}',
                '{{command_processor_node_list}}',
                '{{event_nodes}}',
                '{{event_node_list}}',
                '{{projector_nodes}}',
                '{{projector_node_list}}',
                '{{emission_edges}}',
                '{{consumption_edges}}',
            ],
            [
                rtrim($commandProcessorNodes),
                implode(', ', $commandProcessors),
                rtrim($eventNodes),
                implode(', ', $events),
                rtrim($projectorNodes),
                implode(', ', $projectors),
                rtrim($emissionEdges),
                rtrim($consumptionEdges),
            ],
            $template,
        );

        assert(is_string($dot));
        assert($dot !== '');

        $this->renderDot('overview', $dot);
    }
}
