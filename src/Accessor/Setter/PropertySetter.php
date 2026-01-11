<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Setter;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

/**
 * Member setter by property name directly
 */
readonly class PropertySetter implements SetterInterface, TransformableInterface
{
    public function __construct(
        protected string       $propName,
        protected TypeBag      $type,
        protected AttributeBag $attributes,
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

    /**
     * {@inheritDoc}
     */
    public function getTransformerClassOrId(): ?string
    {
        return $this->attributes->get(Transformer::class)?->getTransformerIdOrClass();
    }

    public function hasTransformer(): bool
    {
        return $this->attributes->has(Transformer::class);
    }
}