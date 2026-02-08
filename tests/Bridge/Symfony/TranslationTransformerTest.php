<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Bridge\Symfony;

use ControlBit\Dto\Attribute\Transformers\Enum;
use ControlBit\Dto\Attribute\Transformers\Translate;
use ControlBit\Dto\Contract\Mapper\MapperInterface;
use ControlBit\Dto\Tests\Bridge\Symfony\App\Helper\StringModifier;
use ControlBit\Dto\Tests\Resources\SimpleEnum;
use ControlBit\Dto\Tests\SymfonyTestCase;

class TranslationTransformerTest extends SymfonyTestCase
{
    public function testSimpleTranslate(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            #[Translate]
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('Just Foo', $mappedObject->foo);
    }

    public function testTranslateWithModifier(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            #[Translate(modifier: 'strtoupper')]
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('FOO', $mappedObject->foo);
    }

    public function testTranslateWithModifierOfStaticClass(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            #[Translate(modifier: [StringModifier::class, 'modify'])]
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('FOO', $mappedObject->foo);
    }

    public function testTranslateWithModifierOfStaticClassWithAdditionalArguments(): void
    {
        $from = new class() {
            public string $foo = 'foo';
        };

        $to = new class() {
            #[Translate(modifier: [StringModifier::class, 'modify', ['<-','->']])]
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('<-FOO->', $mappedObject->foo);
    }

    public function testTranslateInCombinationWithEnumTransformer(): void
    {
        $from = new class() {
            public SimpleEnum $foo = SimpleEnum::FOO;
        };

        $to = new class() {
            #[Enum(SimpleEnum::class)]
            #[Translate]
            public string $foo;
        };

        $mappedObject = self::getContainer()->get(MapperInterface::class)->map($from, $to);

        $this->assertEquals('Just Foo', $mappedObject->foo);
    }
}