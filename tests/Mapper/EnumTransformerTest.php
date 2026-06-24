<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Transformers\Enum;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\SimpleEnum;

class EnumTransformerTest extends LibraryTestCase
{
    public function testFromStringToEnum(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            #[Enum(SimpleEnum::class, options: ['reverse' => true])]
            public SimpleEnum $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(SimpleEnum::FOO, $mappedObject->foo);
    }

    public function testFromEnumToString(): void
    {
        $from = new class() {
            public SimpleEnum $foo = SimpleEnum::FOO;
        };

        $to = new class() {
            #[Enum(SimpleEnum::class)]
            public string $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(SimpleEnum::FOO->value, $mappedObject->foo);
    }

    public function testFromArrayOfEnumsToArrayOfStrings(): void
    {
        $from = new class() {
            public array $foo = [SimpleEnum::FOO, SimpleEnum::BAR];
        };

        $to = new class() {
            #[Enum(SimpleEnum::class, options: ['array' => true])]
            public array $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(SimpleEnum::FOO->value, $mappedObject->foo[0]);
        $this->assertEquals(SimpleEnum::BAR->value, $mappedObject->foo[1]);
    }
}