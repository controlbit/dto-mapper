<?php

namespace ControlBit\Dto\Tests\Mapper\ViaSetter;

use ControlBit\Dto\Attribute\Setter;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\ValueException;
use ControlBit\Dto\Tests\LibraryTestCase;

class TypelessTest extends LibraryTestCase
{
    public function testViaSetter(): void
    {
        $from = new class() {
            #[Setter('setBar')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar;

            public function setBar($foo)
            {
                $this->bar = $foo;
            }

            public function getFoo()
            {
                return $this->bar;
            }
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals('foo', $mappedObject->getFoo());
    }

    public function testSetterMethodDoesNotExistsThrowsException(): void
    {
        $from = new class() {
            #[Setter('setFoo')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar = false;

            public function setBar($foo)
            {
                $this->bar = $foo;
            }

            public function isBar()
            {
                return $this->bar;
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '/Provided setter method \"setFoo\(\)\" on \"(.*)\" does not exists\./'
        );

        $this->getMapper()->map($from, $to);
    }

    public function testSetterNonPublicMethodThrowsException(): void
    {
        $from = new class() {
            #[Setter('setBar')]
            public $foo = 'foo';
        };

        $to = new class() {
            private $bar = false;

            private function setBar($foo)
            {
                $this->bar = $foo;
            }

            public function isBar()
            {
                return $this->bar;
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches(
            '/Provided setter method \"setBar\(\)\" on \"(.*)\" must be public\./'
        );

        $this->getMapper()->map($from, $to);
    }
}

