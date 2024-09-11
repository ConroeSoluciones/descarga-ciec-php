<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Model;

use Csfacturacion\Descarga\Model\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /**
     * @return array<string[]>
     */
    public static function validUuidProvider(): array
    {
        return [
            ['123e4567-e89b-12d3-a456-426655440000'],
            ['00000000-0000-0000-0000-000000000000'],
            ['C73BCDCC-2669-4Bf6-81d3-E4AE73FB11FD'],
        ];
    }

    /**
     * @return array<string[]>
     */
    public static function invalidUuidProvider(): array
    {
        return [
            ['c73bcdcc-2669-4bf6-81d3-e4an73fb11fd'],
            ['c73bcdcc26694bf681d3e4ae73fb11fd'],
            ['definitely-not-a-uuid'],
        ];
    }

    /**
     * @dataProvider validUuidProvider
     */
    public function testValidUuid(string $value): void
    {
        $u = new Uuid($value);
        $this->assertIsObject($u);
    }

    /**
     * @dataProvider invalidUuidProvider
     */
    public function testInvalidUuiProvider(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uuid($value);
    }

    public function testMustHaveToString(): void
    {
        $u = new Uuid('00000000-0000-0000-0000-000000000000');
        $s = "Hello $u";

        $this->assertEquals('Hello 00000000-0000-0000-0000-000000000000', $s);
    }
}
