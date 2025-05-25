<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostRequest::class)]
#[Group('framework')]
#[Group('framework/http')]
#[Small]
final class PostRequestTest extends TestCase
{
    public function testHasUri(): void
    {
        $uri = 'uri';

        $request = PostRequest::from($uri, 'body');

        $this->assertSame($uri, $request->uri());
    }

    public function testHasBody(): void
    {
        $body = 'body';

        $request = PostRequest::from('uri', $body);

        $this->assertSame($body, $request->body());
    }

    public function testCanBeQueriedForItsType(): void
    {
        $request = PostRequest::from('uri', '');

        $this->assertTrue($request->isPostRequest());
        $this->assertFalse($request->isGetRequest());
    }
}
