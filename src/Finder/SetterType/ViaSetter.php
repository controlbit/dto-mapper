<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Accessor\Setter\MethodSetter;
use ControlBit\Dto\Attribute\Setter as SetterAttribute;
use ControlBit\Dto\Bag\AttributeBag;
use ControlBit\Dto\Bag\TypeBag;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\Exception\InvalidArgumentException;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;
use ControlBit\Dto\Util\TypeTool;
use function ControlBit\Dto\instantiate_attributes;

final class ViaSetter implements SetterResolverInterface
{
    public function resolve(
        PropertyMetadata $sourcePropertyMetadata,
        ObjectMetadata   $destinationMetaData,
    ): ?SetterInterface {
        /** @var SetterAttribute|null $setterAttribute */
        $setterAttribute = $sourcePropertyMetadata->getAttributes()->get(SetterAttribute::class);

        if (null === $setterAttribute) {
            return null;
        }

        if (null === $destinationMetaData->getMethod($setterAttribute->getName())) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Provided setter method "%s()" on "%s" does not exists.',
                    $setterAttribute->getName(),
                    $destinationMetaData->getFcqn(),
                )
            );
        }

        $reflectionMethod = new \ReflectionMethod($destinationMetaData->getFcqn(), $setterAttribute->getName());

        if (!$reflectionMethod->isPublic()) {
            throw new InvalidArgumentException(
                \sprintf(
                    'Provided setter method "%s()" on "%s" must be public.',
                    $setterAttribute->getName(),
                    $destinationMetaData->getFcqn(),
                )
            );
        }

        $param = $reflectionMethod->getParameters()[0] ?? null;
        $type  = new TypeBag(null !== $param ? TypeTool::getReflectionTypes($param) : []);

        return new MethodSetter(
            $reflectionMethod->getName(),
            $type,
            AttributeBag::fromArray(instantiate_attributes($reflectionMethod)),
        );
    }
}