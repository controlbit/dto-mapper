<?php
declare(strict_types=1);

namespace ControlBit\Dto\Accessor\Getter;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\GetterInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

/**
 * Get Accessor for method name
 */
final readonly class MethodGetter implements GetterInterface, TransformableInterface
{
    public function __construct(private string $methodName, private TypeBag $type, private AttributeBag $attributes)
    {
    }

    public function get(object $object): mixed
    {
        return $object->{$this->methodName}();
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function hasTransformersAttributes(): bool
    {
        return $this->attributes->has(Transformer::class);
    }

    public function getTransformerAttributes(): array
    {
        return $this->attributes->getAllOf(Transformer::class);
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }
}