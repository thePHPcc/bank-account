<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const PHP_EOL;
use function array_last;
use function array_map;
use function array_merge;
use function array_unique;
use function assert;
use function explode;
use function file_get_contents;
use function implode;
use function is_string;
use function mb_strlen;
use function rtrim;
use function sprintf;
use function str_replace;

final readonly class OverviewRenderer
{
    /**
     * @param array<class-string, array{consumes: list<class-string>, emits: list<class-string>}> $commands
     *
     * @return non-empty-string
     */
    public function render(array $commands): string
    {
        $commandProcessors     = [];
        $events                = [];
        $commandProcessorNodes = '';
        $eventNodes            = '';
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
                    '  %s -> %s' . PHP_EOL,
                    $commandProcessor,
                    array_last(explode('\\', $event)),
                );
            }

            foreach ($_events['consumes'] as $event) {
                $consumptionEdges .= sprintf(
                    '  %s -> %s' . PHP_EOL,
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

        $template = file_get_contents(__DIR__ . '/templates/overview.dot');

        assert(is_string($template));

        $result = str_replace(
            [
                '{{command_processor_nodes}}',
                '{{command_processor_node_list}}',
                '{{event_nodes}}',
                '{{event_node_list}}',
                '{{emission_edges}}',
                '{{consumption_edges}}',
            ],
            [
                rtrim($commandProcessorNodes),
                implode(', ', $commandProcessors),
                rtrim($eventNodes),
                implode(', ', $events),
                rtrim($emissionEdges),
                rtrim($consumptionEdges),
            ],
            $template,
        );

        assert(is_string($result));
        assert($result !== '');

        return $result;
    }

    /**
     * @param non-empty-string $label
     *
     * @return non-empty-string
     */
    private function formatLabel(string $label): string
    {
        $words = explode(' ', $label);
        $lines = [];
        $line  = '';

        foreach ($words as $word) {
            if (mb_strlen($line) + mb_strlen($word) > 22) {
                $lines[] = $line;
                $line    = '';
            }

            if ($line === '') {
                $line = $word;

                continue;
            }

            $line .= ' ' . $word;
        }

        $lines[] = $line;

        return implode('\n', $lines);
    }
}
