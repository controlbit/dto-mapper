<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\MethodBag;
use ControlBit\Dto\Bag\PropertyBag;
use ControlBit\Dto\Contract\AttributedInterface;

final readonly class ObjectMetadata implements AttributedInterface
{
    public function __construct(
        private string       $fcqn,
        private AttributeBag $attributes,
        private PropertyBag  $properties,
        private MethodBag    $methodsMetadata,
    ) {
    }

    public function getFcqn(): string
    {
        return $this->fcqn;
    }

    public function getProperties(): PropertyBag
    {
        return $this->properties;
    }

    public function getProperty(string $propertyName): ?PropertyMetadata
    {
        return $this->properties->get($propertyName);
    }

    public function getMethod(string $methodName): ?MethodMetadata
    {
        return $this->methodsMetadata->get($methodName);
    }

    public function getAttributes(): AttributeBag
    {
        return $this->attributes;
    }

    public function isDoctrineEntity(): bool
    {
        return $this->attributes->has(\Doctrine\ORM\Mapping\Entity::class);
    }
}