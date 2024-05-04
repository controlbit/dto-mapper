<?php

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\MetaData\ObjectMetadata;
use ControlBit\Dto\MetaData\PropertyMetadata;

interface SetterResolverInterface
{
    public function resolve(
        PropertyMetadata $sourcePropertyMetadata,
        ObjectMetadata   $destinationMetaData,
    ): ?SetterInterface;
}