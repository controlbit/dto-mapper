<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\NestedDto;

final class DtoWithArrayTest extends LibraryTestCase
{
    public function testNestedMultipleDto(): void
    {
        $input = [
            'nestedDtoArray' => [
                [
                    'nestedDtoArray' => [
                        [
                            'nestedDtoArray' => [],
                            'scalarArray'    => [7, 8, 9],
                            'scalar'         => 'baz',
                        ],
                    ],
                    'scalarArray'    => [4, 5, 6],
                    'scalar'         => 'bar',
                ],
            ],
            'scalarArray'    => [1, 2, 3],
            'scalar'         => 'foo',
        ];

        /** @var NestedDto $dto */
        $dto = $this->getMapper()->map($input, NestedDto::class);

        self::assertEquals('foo', $dto->getScalar());
        self::assertEquals([1, 2, 3], $dto->getScalarArray());

        self::assertEquals('bar', $dto->getNestedDtoArray()[0]->getScalar());
        self::assertEquals([4, 5, 6], $dto->getNestedDtoArray()[0]->getScalarArray());

        self::assertEquals('baz', $dto->getNestedDtoArray()[0]->getNestedDtoArray()[0]->getScalar());
        self::assertEquals([7, 8, 9], $dto->getNestedDtoArray()[0]->getNestedDtoArray()[0]->getScalarArray());
    }
}