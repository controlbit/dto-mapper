<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;

final class Direct implements SetterResolverInterface
{
    public function __construct()
    {
    }

    public function resolve(
        ClassMetadata $destinationMetaData,
        MapMetadata   $mapMetadata,
    ): ?SetterInterface {

        if (null === $mapMetadata->getDestinationMember()) {
            return null;
        }

        $toPropertyMetadata = $destinationMetaData->getProperty($mapMetadata->getDestinationMember());

        return $toPropertyMetadata?->getAccessor()->getSetter();
    }
}