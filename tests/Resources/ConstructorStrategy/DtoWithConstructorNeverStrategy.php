<?php
declare(strict_types=1);

namespace ControlBit\Dto\Tests\Resources\ConstructorStrategy;

use ControlBit\Dto\Attribute\Dto;
use ControlBit\Dto\Enum\ConstructorStrategy;

#[Dto(constructorStrategy: ConstructorStrategy::NEVER)]
final class DtoWithConstructorNeverStrategy
{
    public string $foo;
    public ?string $bar;

    public function __construct(string $foo, ?string $bar = null)
    {
        $this->foo = $foo . $foo;
        $this->bar = $bar;
    }
}