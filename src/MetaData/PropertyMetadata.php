<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData;

use ControlBit\Dto\Attribute\Identifier;
use ControlBit\Dto\Attribute\Ignore;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\AccessorInterface;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\AttributedInterface;

final class PropertyMetadata implements AttributedInterface
{
    public function __construct(
        private readonly string            $name,
        private readonly TypeBag           $type,
        private readonly AccessorInterface $accessor,
        private readonly AttributeBag      $attributes,
        private ?SetterInterface           $destinationSetter = null,
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

    public function shouldIgnore(): bool
    {
        return
            $this->getAttributes()->has(Ignore::class) ||
            $this->getAttributes()->has(Identifier::class) ||
            null === $this->destinationSetter;
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function getDestinationSetter(): ?SetterInterface
    {
        return $this->destinationSetter;
    }

    public function setDestinationSetter(?SetterInterface $destinationSetter): PropertyMetadata
    {
        $this->destinationSetter = $destinationSetter;

        return $this;
    }
}