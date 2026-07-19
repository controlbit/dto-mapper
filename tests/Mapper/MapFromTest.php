<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\NestedDto;

class MapFromTest extends LibraryTestCase
{
    public function testBasicPropertyMappingFromObjectToDifferentProperty(): void
    {
        $from = new class() {
            public $foo = 'foo';
        };

        $to = new class() {
            #[From('foo')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->bar);
    }

    public function testPropertyMappingValueViaGetterUsingToAttribute(): void
    {
        $from = new class() {
            private $foo = 'foo';

            public function getFoo(): string
            {
                return $this->foo;
            }
        };

        $to = new class() {
            #[From(getter: 'getFoo')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->bar);
    }

    public function testCompositeFromOnNestedScalar(): void
    {
        $from = new NestedDto(nestedDto: new NestedDto(scalar: 'foo'));

        $to = new class() {
            #[From(member: 'nestedDto.scalar')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->bar);
    }

    public function testCompositeFromOnNestedObject(): void
    {
        $from = new NestedDto(nestedDto: new NestedDto(nestedDto: new NestedDto(scalar: 'bar')));

        $to = new class() {
            #[From(member: 'nestedDto.nestedDto')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('bar', $mappedObject->bar->scalar);
    }

    public function testAccessingArrayElements(): void
    {
        $array = ['foo' => ['bar', 'baz', 'waldoo']];

        $from = new NestedDto(nestedDto: new NestedDto(scalarArray: $array));

        $to = new class() {
            #[From(member: 'nestedDto.scalarArray.foo.1')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('baz', $mappedObject->bar);
    }

    public function testAccessingArrayElementsStrict(): void
    {
        $array = ['foo' => ['bar', '1' => 'baz', 'waldoo']];

        $from = new NestedDto(nestedDto: new NestedDto(scalarArray: $array));

        $to = new class() {
            #[From(member: 'nestedDto.scalarArray.foo.1')]
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('baz', $mappedObject->bar);
    }
}