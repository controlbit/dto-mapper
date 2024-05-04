<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\MethodBag;
use ControlBit\Dto\Bag\PropertyBag;
use function ControlBit\Dto\instantiate_attributes;

final readonly class ObjectMetadataFactory
{
    public function __construct(
        private PropertyMetadataFactory $propertyMetadataFactory,
        private MethodMetadataFactory   $methodMetadataFactory,
    ) {
    }

    public function create(object $object): ObjectMetadata
    {
        $reflectionObject = new \ReflectionObject($object);

        $properties = new PropertyBag();
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            $properties->add($this->propertyMetadataFactory->create($object, $reflectionProperty->getName()));
        }

        $methods = new MethodBag();
        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            $methods->add($this->methodMetadataFactory->create($object, $reflectionMethod->getName()));
        }

        return new ObjectMetadata(
            \get_class($object),
            AttributeBag::fromArray(instantiate_attributes($reflectionObject)),
            $properties,
            $methods
        );
    }
}