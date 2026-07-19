<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Mapper;

use ControlBit\Dto\Attribute\Ignore;
use ControlBit\Dto\Attribute\Processor;
use ControlBit\Dto\Enum\ProcessorLoad;
use ControlBit\Dto\Tests\LibraryTestCase;
use ControlBit\Dto\Tests\Resources\IncrementProcessor;

class ProcessorTest extends LibraryTestCase
{
    public function testBeforeProcessor(): void
    {
        $from = new class() {
            public int $foo = 3;
        };

        $to = new #[Processor(IncrementProcessor::class, ProcessorLoad::BEFORE_MAPPING)] class()  {
            #[Ignore]
            public int $foo = 1;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(3, $from->foo);
        $this->assertEquals(2, $mappedObject->foo);
    }

    public function testAfterProcessor(): void
    {
        $from = new class() {
            public int $foo = 3;
        };

        $to = new #[Processor(IncrementProcessor::class)] class()  {
            public int $foo = 1;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(4, $mappedObject->foo);
    }

    public function testProcessorOnArraySource(): void
    {
        $from = ['foo' => 2];

        $to = new #[Processor(IncrementProcessor::class)] class()  {
            public int $foo = 1;
        };

        $mappedObject = $this->getMapper()->map($from, $to);

        $this->assertEquals(3, $mappedObject->foo);
    }
}