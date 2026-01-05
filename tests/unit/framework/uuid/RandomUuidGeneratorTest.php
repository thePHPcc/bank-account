<?php declare(strict_types=1);
namespace example\framework\library;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RandomUuidGenerator::class)]
#[UsesClass(Uuid::class)]
#[Group('unit/framework')]
#[Group('unit/framework/uuid')]
#[Small]
final class RandomUuidGeneratorTest extends TestCase
{
    public function test_Generates_random_UUIDs(): void
    {
        $generator = new RandomUuidGenerator;

        $this->assertNotSame($generator->generate(), $generator->generate());
    }
}
