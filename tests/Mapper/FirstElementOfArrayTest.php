<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Transformers\FirstElementOfArray;
use ControlBit\Dto\Tests\LibraryTestCase;

class FirstElementOfArrayTest extends LibraryTestCase
{
    public function testMappingObjectWithScalarToArrayOfOne(): void
    {
        $from = new class() {
            #[FirstElementOfArray]
            public array $foo = ['baz', 'qux', 'fruit'];
        };

        $to = new class() {
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        /** @var string $mappedValue */
        $mappedValue = $mappedObject->foo;

        $this->assertEquals('baz', $mappedValue);
    }
}