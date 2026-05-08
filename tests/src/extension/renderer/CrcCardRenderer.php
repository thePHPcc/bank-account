<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const ENT_HTML5;
use const ENT_QUOTES;
use function array_last;
use function array_map;
use function assert;
use function explode;
use function file_get_contents;
use function htmlspecialchars;
use function is_string;
use function str_replace;

final readonly class CrcCardRenderer extends Renderer
{
    /**
     * @param array<class-string, array{title: non-empty-string, responsibilities: list<non-empty-string>, collaborators: list<class-string>}> $crcCards
     */
    public function render(array $crcCards): void
    {
        if ($crcCards === []) {
            return;
        }

        $template = file_get_contents(__DIR__ . '/../templates/crc_card.dot');

        assert(is_string($template));
        assert($template !== '');

        foreach ($crcCards as $sut => $card) {
            $dot = str_replace(
                ['{{title}}', '{{responsibilities}}', '{{collaborators}}'],
                [
                    self::escape($card['title']),
                    self::renderList($card['responsibilities']),
                    self::renderList(
                        array_map(self::shortName(...), $card['collaborators']),
                    ),
                ],
                $template,
            );

            assert(is_string($dot));
            assert($dot !== '');

            $this->renderDot(self::shortName($sut) . '_crc', $dot);
        }
    }

    /**
     * @param list<non-empty-string> $lines
     */
    private static function renderList(array $lines): string
    {
        if ($lines === []) {
            return '<I>(none)</I><BR ALIGN="LEFT"/>';
        }

        $rendered = '';

        foreach ($lines as $line) {
            $rendered .= '&#8226; ' . self::escape($line) . '<BR ALIGN="LEFT"/>';
        }

        return $rendered;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * @param class-string $fqcn
     *
     * @return non-empty-string
     */
    private static function shortName(string $fqcn): string
    {
        $short = array_last(explode('\\', $fqcn));

        assert($short !== '');

        return $short;
    }
}
