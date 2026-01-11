<?php
declare(strict_types=1);

namespace ControlBit\Dto\MetaData\Property;

use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Finder\AccessorFinder;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

final readonly class PropertyMetadataFactory
{
    public function __construct(private AccessorFinder $accessorFinder)
    {
    }

    public function create(object|string $subject, string $propertyName): PropertyMetadata
    {
        $reflectionObject   = \is_object($subject) ? new \ReflectionObject($subject) : new \ReflectionClass($subject);
        $reflectionProperty = $reflectionObject->getProperty($propertyName);
        $type               = $this->getType($reflectionProperty);

        return new PropertyMetadata(
            $reflectionProperty->name,
            $type,
            $this->accessorFinder->find($reflectionObject, $reflectionProperty),
            AttributeBag::fromArray(instantiate_attributes($reflectionProperty)),
            $reflectionProperty->isPublic(),
        );
    }

    private function getType(\ReflectionProperty $reflectionProperty): TypeBag
    {
        return new TypeBag(TypeTool::getReflectionTypes($reflectionProperty));
    }

}