<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Property;

use ControlBit\Dto\Attribute\Identifier;
use ControlBit\Dto\Attribute\Ignore;
use ControlBit\Dto\Attribute\Transformer;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\AccessorInterface;
use ControlBit\Dto\Contract\AttributedInterface;
use ControlBit\Dto\Contract\Transformer\TransformableInterface;

final class PropertyMetadata implements AttributedInterface, TransformableInterface
{
    public function __construct(
        private readonly string            $name,
        private readonly TypeBag           $type,
        private readonly AccessorInterface $accessor,
        private readonly AttributeBag      $attributes,
        private readonly bool              $isPublic,
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