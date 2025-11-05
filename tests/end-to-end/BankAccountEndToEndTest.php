<?php declare(strict_types=1);
namespace example\bankaccount\application;

use function assert;
use function defined;
use function file_get_contents;
use function is_string;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[RunTestsInSeparateProcesses]
#[Large]
#[TestDox('End-to-End Tests for /bank-account')]
final class BankAccountEndToEndTest extends TestCase
{
    #[TestDox('GET request to /bank-account sends response with HTML projection')]
    public function testGetRequestToBankAccountSendsResponseWithHtmlProjection(): void
    {
        $response = $this->request('/bank-account');

        $this->assertStringEqualsFile(
            __DIR__ . '/../expectation/bank-account.html',
            $response['body'],
        );
    }

    /**
     * @param non-empty-string $uri
     *
     * @return array{body: string, headers: list<string>}
     */
    private function request(string $uri): array
    {
        assert(defined('TEST_WEB_SERVER_BASE_URL'));
        assert(is_string(TEST_WEB_SERVER_BASE_URL));

        $body = @file_get_contents(TEST_WEB_SERVER_BASE_URL . $uri);

        if ($body === false) {
            $this->markTestSkipped('Could not connect to ' . TEST_WEB_SERVER_BASE_URL);
        }

        return [
            'body'    => $body,
            'headers' => $http_response_header,
        ];
    }
}
