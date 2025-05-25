<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(GetRequest::class)]
#[CoversClass(Request::class)]
#[Group('framework')]
#[Group('framework/http')]
#[Small]
final class GetRequestTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testCanBeCreatedFromSuperGlobals(): void
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

    public function testHasUri(): void
    {
        $uri = 'uri';

        $request = GetRequest::from($uri, ['key' => 'value']);

        $this->assertSame($uri, $request->uri());
    }

    public function testCanQueryParametersThatExist(): void
    {
        $request = GetRequest::from('uri', ['key' => 'value']);

        $this->assertTrue($request->has('key'));
        $this->assertSame('value', $request->get('key'));
    }

    public function testCannotQueryParametersThatDoNotExist(): void
    {
        $request = GetRequest::from('uri', []);

        $this->assertFalse($request->has('key'));

        $this->expectException(ParameterDoesNotExistException::class);

        $request->get('key');
    }

    public function testCanBeQueriedForItsType(): void
    {
        $request = GetRequest::from('uri', []);

        $this->assertTrue($request->isGetRequest());
        $this->assertFalse($request->isPostRequest());
    }
}
