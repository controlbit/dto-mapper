<?php

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;
use ControlBit\Dto\MetaData\Property\PropertyMetadata;

interface SetterResolverInterface
{
    public function resolve(
        ClassMetadata    $destinationMetaData,
        MapMetadata $mapMetadata
    ): ?SetterInterface;
}