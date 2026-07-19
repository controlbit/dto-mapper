<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Enum\ConstructorStrategy;
use ControlBit\Dto\Exception\MissingArgumentException;
use ControlBit\Dto\Factory;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\ConstructorStrategy\DtoWithConstructor;
use ControlBit\Dto\Tests\Resources\ConstructorStrategy\DtoWithConstructorAlwaysStrategy;
use ControlBit\Dto\Tests\Resources\ConstructorStrategy\DtoWithConstructorNeverStrategy;
use ControlBit\Dto\Tests\Resources\ConstructorStrategy\DtoWithConstructorOptionalStrategy;
use ControlBit\Dto\Tests\Resources\DtoWithoutConstructor;

class ConstructorTest extends LibraryTestCase
{
    public function testOptionalConstructor(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithConstructorOptionalStrategy::class);

        $this->assertEquals('foofoo', $mappedObject->foo);
    }

    public function testOptionalConstructorWhenMissingConstructor(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithoutConstructor::class);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testNeverConstructorStrategy(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithConstructorNeverStrategy::class);

        $this->assertEquals('foo', $mappedObject->foo);
    }

    public function testAlwaysConstructorStrategy(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $mappedObject = $this->getMapper()->map($from, DtoWithConstructorAlwaysStrategy::class);

        $this->assertEquals('foofoo', $mappedObject->foo);
    }

    public function testAlwaysConstructorWithoutAllArgumentsAvailableThrowsException(): void
    {
        $from = new class() {};

        $this->expectException(MissingArgumentException::class);

        Factory::create(true, ConstructorStrategy::ALWAYS)->map($from, DtoWithConstructor::class);
    }
}