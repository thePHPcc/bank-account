<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use function array_last;
use function assert;
use function explode;
use function file_get_contents;
use function implode;
use function is_string;
use function mb_strlen;
use function sprintf;
use function str_replace;
use PHPUnit\Event\Code\TestMethod;

final readonly class GivenWhenThenRenderer extends Renderer
{
    /**
     * @param list<non-empty-string> $given
     * @param non-empty-string       $when
     * @param list<non-empty-string> $then
     */
    public function render(TestMethod $test, array $given, string $when, array $then): void
    {
        $target = sprintf(
            '%s_%s',
            array_last(explode('\\', $test->className())),
            $test->methodName(),
        );

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

        $template = file_get_contents(__DIR__ . '/../templates/given_when_then.dot');

        assert(is_string($template));

        $dot = str_replace(
            [
                '{{given}}',
                '{{when}}',
                '{{then}}',
                '{{edges}}',
            ],
            [
                $givenBuffer,
                $when,
                $thenBuffer,
                implode(' -> ', $edges),
            ],
            $template,
        );

        assert(is_string($dot));
        assert($dot !== '');

        $this->renderDot($target, $dot);
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
