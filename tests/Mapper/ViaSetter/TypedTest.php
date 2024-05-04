<?php

namespace ControlBit\Dto\Tests\Mapper\ViaSetter;

use ControlBit\Dto\Attribute\Setter;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\Exception\ValueException;
use ControlBit\Dto\Tests\LibraryTestCase;

class TypedTest extends LibraryTestCase
{
    public function testViaSetter(): void
    {
        $from = new class() {
            #[Setter('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private string $bar;

            public function setBar(string $foo)
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

    public function testViaSetterWrongType(): void
    {
        $from = new class() {
            #[Setter('setBar')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private bool $bar = false;

            public function setBar(bool $foo)
            {
                $this->bar = $foo;
            }

            public function isBar(): bool
            {
                return $this->bar;
            }
        };

        $this->expectException(ValueException::class);
        $this->expectExceptionMessageMatches(
            '/Cannot map value "foo" of "(.*)" to destination object of "(.*)"\. Check if writable\/callable and correct type\(s\)/'
        );

        $this->getMapper()->map($from, $to);
    }

    public function testSetterMethodDoesNotExistsThrowsException(): void
    {
        $from = new class() {
            #[Setter('setFoo')]
            public string $foo = 'foo';
        };

        $to = new class() {
            private bool $bar = false;

            public function setBar(bool $foo)
            {
                $this->bar = $foo;
            }

            public function isBar(): bool
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
            public string $foo = 'foo';
        };

        $to = new class() {
            private bool $bar = false;

            private function setBar(bool $foo)
            {
                $this->bar = $foo;
            }

            public function isBar(): bool
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

