<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Transformers\Collection;
use ControlBit\Dto\Tests\LibraryTestCase;
use Doctrine\Common\Collections\ArrayCollection;

class ArrayCollectionTest extends LibraryTestCase
{
    public function testMappingObjectWithArrayToObjectWithArrayCollection(): void
    {
        $from = new class() {
            public array $foo = ['baz', 'qux', 'fruit'];
        };

        $to = new class() {
            #[Collection(['reverse' => true])]
            public ArrayCollection $foo;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        /** @var ArrayCollection $mappedValue */
        $mappedValue = $mappedObject->foo;

        $this->assertEquals(3, $mappedValue->count());
        $this->assertEquals('baz', $mappedValue->get(0));
        $this->assertEquals('qux', $mappedValue->get(1));
        $this->assertEquals('fruit', $mappedValue->get(2));
    }
}