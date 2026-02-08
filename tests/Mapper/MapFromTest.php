<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\From;
use ControlBit\Dto\Tests\LibraryTestCase;

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
}