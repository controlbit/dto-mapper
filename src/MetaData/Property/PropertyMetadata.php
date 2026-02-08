<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Property;

use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\AccessorInterface;
use ControlBit\Dto\Contract\AttributedInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

final readonly class PropertyMetadata implements AttributedInterface, TransformableInterface
{
    public function __construct(
        private string            $name,
        private TypeBag           $type,
        private AccessorInterface $accessor,
        private AttributeBag      $attributes,
        private bool              $isPublic,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): TypeBag
    {
        return $this->type;
    }

    public function getAccessor(): AccessorInterface
    {
        return $this->accessor;
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function hasTransformersAttributes(): bool
    {
        return $this->attributes->has(Transformer::class);
    }

    public function getTransformerAttributes(): array
    {
        return $this->attributes->getAllOf(Transformer::class);
    }
}