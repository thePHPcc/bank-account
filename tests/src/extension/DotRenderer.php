<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use function assert;
use function file_get_contents;
use function implode;
use function is_string;
use function sprintf;
use function str_replace;

final readonly class DotRenderer
{
    /**
     * @param list<non-empty-string> $given
     * @param non-empty-string       $when
     * @param list<non-empty-string> $then
     *
     * @return non-empty-string
     */
    public function render(array $given, string $when, array $then): string
    {
        $edges       = [];
        $givenBuffer = '';

        foreach ($given as $index => $label) {
            $givenBuffer .= sprintf(
                <<<'EOT'

        given_%d [label="%s", fillcolor="#fcaf3e",color="#f57900"];
EOT,
                $index + 1,
                $this->formatLabel($label),
            );

            $edges[] = sprintf('given_%d', $index + 1);
        }

        $edges[]    = 'command';
        $thenBuffer = '';

        foreach ($then as $index => $label) {
            $thenBuffer .= sprintf(
                <<<'EOT'

        then_%d [label="%s", fillcolor="#fcaf3e",color="#f57900"];
EOT,
                $index + 1,
                $this->formatLabel($label),
            );

            $edges[] = sprintf('then_%d', $index + 1);
        }

        $template = file_get_contents(__DIR__ . '/templates/events.dot');

        assert(is_string($template));

        $result = str_replace(
            [
                '{{{given}}}',
                '{{{when}}}',
                '{{{then}}}',
                '{{{edges}}}',
            ],
            [
                $givenBuffer,
                $when,
                $thenBuffer,
                implode(' -> ', $edges),
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
        return str_replace(
            ['at price', 'from'],
            ['\nat price', '\nfrom'],
            $label,
        );
    }
}
