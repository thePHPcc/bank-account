<?php declare(strict_types=1);
namespace example\framework\event\test\extension;

use const DIRECTORY_SEPARATOR;
use function exec;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

abstract readonly class Renderer
{
    /**
     * @var non-empty-string
     */
    private string $targetDirectory;

    /**
     * @var 'dot'|'pdf'|'png'|'svg'
     */
    private string $format;

    /**
     * @param non-empty-string        $targetDirectory
     * @param 'dot'|'pdf'|'png'|'svg' $format
     */
    public function __construct(string $targetDirectory, string $format)
    {
        $this->targetDirectory = $targetDirectory;
        $this->format          = $format;
    }

    protected function renderDot(string $target, string $dot): void
    {
        $this->createDirectory($this->targetDirectory);

        $target = sprintf(
            '%s%s%s.%s',
            $this->targetDirectory,
            DIRECTORY_SEPARATOR,
            $target,
            $this->format,
        );

        if ($this->format === 'dot') {
            file_put_contents($target, $dot);

            return;
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'graphviz');

        file_put_contents($tmpFile, $dot);

        exec(
            sprintf(
                'dot -T%s -o %s %s > /dev/null 2>&1',
                $this->format,
                $target,
                $tmpFile,
            ),
        );

        unlink($tmpFile);
    }

    /**
     * @param non-empty-string $directory
     */
    private function createDirectory(string $directory): bool
    {
        return !(!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory));
    }
}
