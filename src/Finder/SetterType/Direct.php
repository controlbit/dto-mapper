<?php
declare(strict_types=1);

namespace ControlBit\Dto\Finder\SetterType;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\Contract\Mapper\SetterResolverInterface;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

final class Direct implements SetterResolverInterface
{
    public function resolve(
        PropertyMetadata $sourcePropertyMetadata,
        ObjectMetadata   $destinationMetaData,
    ): ?SetterInterface {
        $toPropertyMetadata = $destinationMetaData->getProperty($sourcePropertyMetadata->getName());

        if (null === $toPropertyMetadata) {
            return null;
        }

        return $toPropertyMetadata->getAccessor()->getSetter();
    }
}