<?php declare(strict_types=1);
namespace example\bankaccount\application;

use example\framework\http\GetRequest;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
#[Large]
#[TestDox('Edge-to-Edge Tests for /bank-account')]
final class BankAccountEdgeToEdgeTest extends TestCase
{
    #[TestDox('GET request to /bank-account sends response with HTML projection (tested through Kernel::run()')]
    public function testGetRequestToBankAccountSendsResponseWithHtmlProjection(): void
    {
        $request = GetRequest::from('/bank-account', []);

        $response = (new ApplicationFactory)->createApplication()->run($request);

        $this->assertStringEqualsFile(
            __DIR__ . '/../expectation/bank-account.html',
            $response->body(),
        );
    }

    #[RunInSeparateProcess]
    #[TestDox('GET request to /bank-account sends response with HTML projection (tested through index.php)')]
    public function testGetRequestToBankAccountSendsResponseWithHtmlProjectionSlightlyLarger(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/bank-account';

        require __DIR__ . '/../../public/index.php';

        $this->assertStringEqualsFile(
            __DIR__ . '/../expectation/bank-account.html',
            $this->getActualOutputForAssertion(),
        );
    }
}
