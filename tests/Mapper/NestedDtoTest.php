<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\NestedDto;

final class NestedDtoTest extends LibraryTestCase
{
    public function testNestedDto(): void
    {
        $input = [
            'nestedDto' => [
                'scalarArray' => [1, 2, 3],
                'scalar'      => 'foo',
            ],
        ];

        /** @var NestedDto $dto */
        $dto = $this->getMapper()->map($input, NestedDto::class);

        self::assertEquals('foo', $dto->getNestedDto()->getScalar());
        self::assertEquals([1, 2, 3], $dto->getNestedDto()->getScalarArray());
    }
}