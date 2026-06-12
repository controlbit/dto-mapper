<?php

declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Transformers\Uuid as UuidAttribute;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Transformer\UuidTransformer;
use Symfony\Component\Uid\Uuid;

class UuidTransformerTest extends LibraryTestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function provideUuidStrings(): array
    {
        return [
            'nil' => ['00000000-0000-0000-0000-000000000000'],
            'max' => ['ffffffff-ffff-ffff-ffff-ffffffffffff'],
            'v1'  => ['af908c78-668e-11f1-8280-e11cea97e32c'],
            'v3'  => ['7a0f53a7-5fce-319c-adb6-de700e5f6574'],
            'v4'  => ['ebb7c23f-2f24-43af-8d03-32c826301ce1'],
            'v5'  => ['7bd452ec-7fdb-5064-b0cb-5509b9e2650e'],
            'v6'  => ['1f1668ea-f909-6736-b9d0-dd1c4868ff7d'],
            'v7'  => ['019ebd25-fdef-7863-b199-9714b487907b'],
            'v8'  => ['550e8400-e29b-8814-a716-446655440000'],
        ];
    }

    /**
     * @dataProvider provideUuidStrings
     */
    public function testFromUuidToString(string $uuidString): void
    {
        $uuid = Uuid::fromString($uuidString);

        $from = new class($uuid) {
            public function __construct(public Uuid $foo)
            {
            }
        };

        $to = new class() {
            #[UuidAttribute]
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals($uuidString, $mappedObject->foo);
    }

    /**
     * @dataProvider provideUuidStrings
     */
    public function testFromStringToUuid(string $uuidString): void
    {
        $from = new class() {
            public string $foo = '';
        };
        $from->foo = $uuidString;

        $to = new class() {
            #[UuidAttribute(options: ['reverse' => true])]
            public Uuid $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertInstanceOf(Uuid::class, $mappedObject->foo);
        $this->assertEquals($uuidString, $mappedObject->foo->toRfc4122());
    }

    public function testTransformNull(): void
    {
        $transformer = new UuidTransformer();

        $this->assertNull($transformer->transform(null));
    }

    public function testTransformNonUuidThrowsException(): void
    {
        $transformer = new UuidTransformer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided value for Uuid Transformer is not Uuid.');

        $transformer->transform('not-a-uuid');
    }

    public function testReverseNull(): void
    {
        $transformer = new UuidTransformer();

        $this->assertNull($transformer->reverse(null));
    }

    public function testReverseNonStringThrowsException(): void
    {
        $transformer = new UuidTransformer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided value for Uuid transformer is not a string.');

        $transformer->reverse(12345);
    }

    public function testReverseInvalidUuidThrowsException(): void
    {
        $transformer = new UuidTransformer();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provided value for Uuid transformer is not a valid uuid.');

        $transformer->reverse('not-a-uuid');
    }
}
