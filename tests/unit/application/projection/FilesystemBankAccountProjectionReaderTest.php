<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\library\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FilesystemBankAccountProjectionReader::class)]
#[UsesClass(Uuid::class)]
#[Small]
final class FilesystemBankAccountProjectionReaderTest extends TestCase
{
    public function testReadsHtmlProjectionOfBankAccount(): void
    {
        $accountId = Uuid::from('81e61f38-5c81-490b-86cd-09e1b418ad61');

        $path = __DIR__ . '/../../../expectation/bank-account.html';

        $this->assertStringEqualsFile(
            $path,
            new FilesystemBankAccountProjectionReader($path)->read($accountId),
        );
    }
}
