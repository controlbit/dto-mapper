<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\To;
use ControlBit\Dto\Attribute\Transformers\Enum;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\SimpleEnum;

class EnumTransformerTest extends LibraryTestCase
{
    public function testToEnum(): void
    {
        $from = new class() {
            #[Enum(SimpleEnum::class)]
            public string $foo = 'foo';
        };

        $to = new class() {
            public SimpleEnum $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(SimpleEnum::FOO, $mappedObject->foo);
    }
}