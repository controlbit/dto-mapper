<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Getter;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

/**
 * Get Accessor that access via property
 */
final readonly class PropertyGetter implements GetterInterface, TransformableInterface
{
    public function __construct(private string $propName, private TypeBag $type, private AttributeBag $attributes)
    {
    }

    public function get(object $object): mixed
    {
        return $object->{$this->propName};
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function getClassOrId(): ?string
    {
        return $this->attributes->get(Transformer::class)?->getTransformerIdOrClass();
    }

    public function getOptions(): ?array
    {
        return $this->attributes->get(Transformer::class)?->getOptions();
    }

    public function hasTransformer(): bool
    {
        return $this->attributes->has(Transformer::class);
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }
}