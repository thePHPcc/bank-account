<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
#[UsesClass(GetRequest::class)]
#[UsesClass(PostRequest::class)]
#[Group('framework')]
#[Group('framework/http')]
#[RunTestsInSeparateProcesses]
#[Small]
final class RequestTest extends TestCase
{
    public function test_Can_create_GetRequest_from_super_globals(): void
    {
        $_SERVER['REQUEST_URI']    = 'uri';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET                      = ['key' => 'value'];

        $request = Request::fromSuperGlobals();

        $this->assertInstanceOf(GetRequest::class, $request);
        $this->assertSame('uri', $request->uri());
        $this->assertTrue($request->has('key'));
        $this->assertSame('value', $request->get('key'));
    }

    public function test_Can_create_PostRequest_from_super_globals(): void
    {
        $_SERVER['REQUEST_URI']    = 'uri';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $request = Request::fromSuperGlobals();

        $this->assertInstanceOf(PostRequest::class, $request);
        $this->assertSame('uri', $request->uri());
    }
}
