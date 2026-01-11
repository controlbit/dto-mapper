<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Class;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\MethodBag;
use ControlBit\Dto\Bag\PropertyBag;
use ControlBit\Dto\MetaData\Method\MethodMetadataFactory;
use ControlBit\Dto\MetaData\Property\PropertyMetadataFactory;
use function ControlBit\Dto\instantiate_attributes;

final readonly class ClassMetadataFactory
{
    public function __construct(
        private PropertyMetadataFactory $propertyMetadataFactory,
        private MethodMetadataFactory   $methodMetadataFactory,
    ) {
    }

    public function create(object|string $subject): ClassMetadata
    {
        $reflection = is_object($subject) ? new \ReflectionObject($subject) : new \ReflectionClass($subject);

        $properties = new PropertyBag();
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $properties->add($this->propertyMetadataFactory->create($subject, $reflectionProperty->getName()));
        }

        $methods = new MethodBag();
        foreach ($reflection->getMethods() as $reflectionMethod) {
            $methods->add($this->methodMetadataFactory->create($reflection, $reflectionMethod->getName()));
        }

        return new ClassMetadata(
            $reflection->getName(),
            AttributeBag::fromArray(instantiate_attributes($reflection)),
            $properties,
            $methods
        );
    }
}