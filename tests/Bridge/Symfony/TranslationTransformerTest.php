<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony;

use ControlBit\Dto\Attribute\Transformers\Translate;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Tests\SymfonyTestCase;

class TranslationTransformerTest extends SymfonyTestCase
{
    public function testToEnum(): void
    {
        $from = new class() {
            #[Translate]
            public string $foo = 'foo';
        };

        $to = new class() {
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('Just Foo', $mappedObject->foo);
    }
}