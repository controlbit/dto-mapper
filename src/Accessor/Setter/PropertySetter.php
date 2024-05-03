<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Setter;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;

/**
 * Member setter by property name directly
 */
final readonly class PropertySetter implements SetterInterface
{
    public function __construct(
        private string       $propName,
        private TypeBag      $type,
        private AttributeBag $attributes,
    ) {
    }

    public function set(object $object, mixed $value): void
    {
        $object->{$this->propName} = $value;
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