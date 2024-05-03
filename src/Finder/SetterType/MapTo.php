<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Attribute\MapTo as MapToAttribute;
use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

final class MapTo implements SetterResolverInterface
{
    public function resolve(
        PropertyMetadata $sourcePropertyMetadata,
        ObjectMetadata   $destinationMetaData,
    ): ?SetterInterface {
        /** @var MapToAttribute|null $mapToAttribute */
        $mapToAttribute = $sourcePropertyMetadata->getAttributes()->get(MapToAttribute::class);

        if (null === $mapToAttribute) {
            return null;
        }

        $toPropertyMetadata = $destinationMetaData->getProperty($mapToAttribute->getPropertyName());

        if (null === $toPropertyMetadata) {
            return null;
        }

        return $toPropertyMetadata->getAccessor()->getSetter();
    }
}