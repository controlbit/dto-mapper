<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Accessor\Setter\MethodSetter;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

final class ViaSetter implements SetterResolverInterface
{
    public function resolve(
        ClassMetadata $destinationMetaData,
        MapMetadata   $mapMetadata,
    ): ?SetterInterface {

        $method = match (true) {
            $mapMetadata->getDestinationMethod() !== null => $destinationMetaData->getMethod($mapMetadata->getDestinationMethod()),
            $mapMetadata->getDestinationMember() !== null => $destinationMetaData->getMethod('set'.\ucfirst($mapMetadata->getDestinationMember())),
            default                                       => null,
        };

        if (null === $method) {
            return null;
        }

        $reflectionMethod = new \ReflectionMethod($destinationMetaData->getFqcn(), $method->getName());
        $param            = $reflectionMethod->getParameters()[0] ?? null;
        $type             = new TypeBag(null !== $param ? TypeTool::getReflectionTypes($param) : []);

        return new MethodSetter(
            $reflectionMethod->getName(),
            $type,
            AttributeBag::fromArray(instantiate_attributes($reflectionMethod)),
        );
    }
}