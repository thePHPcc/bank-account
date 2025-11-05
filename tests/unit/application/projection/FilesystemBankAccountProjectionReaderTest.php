<?php declare(strict_types=1);
namespace example\bankaccount\application;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilesystemBankAccountProjectionReader::class)]
#[Small]
final class FilesystemBankAccountProjectionReaderTest extends TestCase
{
    public function testReadsHtmlProjectionOfBankAccount(): void
    {
        $path = __DIR__ . '/../../../expectation/bank-account.html';

        $this->assertStringEqualsFile(
            $path,
            new FilesystemBankAccountProjectionReader($path)->read(),
        );
    }
}
