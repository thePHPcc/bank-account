<?php declare(strict_types=1);
namespace example\framework\http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
#[Group('unit/framework')]
#[Group('unit/framework/http')]
#[Small]
final class ResponseTest extends TestCase
{
    public function testHasInitiallyNoHeaders(): void
    {
        $this->assertSame([], (new Response)->headers());
    }

    public function testHeaderCanBeAdded(): void
    {
        $response = new Response;
        $header   = 'the-header';

        $response->addHeader($header);

        $this->assertSame([$header], $response->headers());
    }

    public function testHasInitiallyEmptyBody(): void
    {
        $this->assertSame('', (new Response)->body());
    }

    public function testBodyCanBeSet(): void
    {
        $response = new Response;
        $body     = 'the-body';

        $response->setBody($body);

        $this->assertSame($body, $response->body());
    }

    public function testCanBeSent(): void
    {
        $response = new Response;
        $body     = 'the-body';
        $header   = 'the-header';

        $response->addHeader($header);
        $response->setBody($body);

        $this->expectOutputString($body);

        $response->send();
    }
}
