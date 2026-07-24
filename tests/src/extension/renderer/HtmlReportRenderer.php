<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;
use const PHP_EOL;
use function assert;
use function count;
use function file_get_contents;
use function htmlspecialchars;
use function is_string;
use function ksort;
use function rtrim;
use function sprintf;
use function str_replace;

final readonly class HtmlReportRenderer extends Renderer
{
    /**
     * @param non-empty-string                                                                              $target
     * @param non-empty-string                                                                              $overview
     * @param list<array{prettifiedClassName: string, prettifiedMethodName: string, dot: non-empty-string}> $useCases
     */
    public function render(string $target, string $overview, array $useCases): void
    {
        $template = file_get_contents(__DIR__ . '/../templates/report.html');

        assert(is_string($template));

        $html = str_replace(
            [
                '{{counts}}',
                '{{overview}}',
                '{{test_classes}}',
            ],
            [
                sprintf('<span class="passed">%d passed</span>', count($useCases)),
                rtrim($this->svg($overview)),
                $this->testClasses($useCases),
            ],
            $template,
        );

        assert(is_string($html));
        assert($html !== '');

        $this->write($target, $html);
    }

    /**
     * @param list<array{prettifiedClassName: string, prettifiedMethodName: string, dot: non-empty-string}> $useCases
     */
    private function testClasses(array $useCases): string
    {
        $groups = [];

        foreach ($useCases as $useCase) {
            $groups[$useCase['prettifiedClassName']][] = $useCase;
        }

        ksort($groups);

        $buffer = '';
        $id     = 0;

        foreach ($groups as $prettifiedClassName => $useCasesOfGroup) {
            $buffer .= sprintf(
                '            <section class="test-class">' . PHP_EOL .
                '                <h2>%s</h2>' . PHP_EOL .
                '                <ul>' . PHP_EOL,
                htmlspecialchars($prettifiedClassName, ENT_QUOTES | ENT_SUBSTITUTE),
            );

            foreach ($useCasesOfGroup as $useCase) {
                $id++;

                $prettifiedMethodName = htmlspecialchars(
                    $useCase['prettifiedMethodName'],
                    ENT_QUOTES | ENT_SUBSTITUTE,
                );

                $buffer .= sprintf(
                    '                    <li class="success">' . PHP_EOL .
                    '                        <button popovertarget="use-case-%1$d">%2$s</button>' . PHP_EOL .
                    '                        <div id="use-case-%1$d" popover>' . PHP_EOL .
                    '                            <h3>%2$s</h3>' . PHP_EOL .
                    '                            <figure>' . PHP_EOL .
                    '%3$s' . PHP_EOL .
                    '                            </figure>' . PHP_EOL .
                    '                        </div>' . PHP_EOL .
                    '                    </li>' . PHP_EOL,
                    $id,
                    $prettifiedMethodName,
                    rtrim($this->svg($useCase['dot'])),
                );
            }

            $buffer .= '                </ul>' . PHP_EOL .
                       '            </section>' . PHP_EOL;
        }

        return rtrim($buffer);
    }
}
