<?php

namespace ControlBit\Dto\Tests\Mapper\Basic;

use ControlBit\Dto\Tests\LibraryTestCase;

class TypelessTest extends LibraryTestCase
{
    public function testBasicPropertyMappingFromObjectToObject(): void
    {
        $from = new class() {
            public $foo = 'foo';
        };

        $to = new class() {
            public $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testBasicPropertyMappingFromObjectToClass(): void
    {
        $from = new class() {
            public $foo = 'foo';
        };

        $to = new class() {
            public $foo;
        };

        $mappedObject = $this->getMapper()->map($from, \get_class($to));

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testBasicPropertyMappingFromArrayToClass(): void
    {
        $from = [ 'foo' => 'foo'];

        $to = new class() {
            public $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->foo);
    }
}