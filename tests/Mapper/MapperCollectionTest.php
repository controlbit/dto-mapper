<?php

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Tests\LibraryTestCase;

class MapperCollectionTest extends LibraryTestCase
{
    public function testMapCollectionMethod(): void
    {
        $from = [
            ['bar' => 'bar'],
            ['bar' => 'baz'],
            ['bar' => 'waldoo'],
        ];

        $to = new class() {
            public $bar;
        };

        $mappedObject = $this->getMapper()->mapCollection($from, $to::class);

        foreach ($from as $index => $mapData) {
            $this->assertEquals($mapData['bar'], $mappedObject[$index]->bar);
        }
    }
}