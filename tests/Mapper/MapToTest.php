<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\To;
use ControlBit\Dto\Tests\LibraryTestCase;

class MapToTest extends LibraryTestCase
{
    public function testBasicPropertyMappingFromObjectToDifferentProperty(): void
    {
        $from = new class() {
            #[To('bar')]
            public $foo = 'foo';
        };

        $to = new class() {
            public $bar;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->bar);
    }
}