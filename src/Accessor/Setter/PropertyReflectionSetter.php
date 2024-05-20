<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Setter;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;

/**
 * Member setter by property name directly, but using Reflection for private properties
 */
final readonly class PropertyReflectionSetter extends PropertySetter
{
    public function set(object $object, mixed $value): void
    {
        $reflection = new \ReflectionObject($object);
        $reflection->getProperty($this->propName)->setValue($object, $value);
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }
}