<?php

namespace ControlBit\Dto\Contract\Mapper;

use ControlBit\Dto\Contract\Accessor\SetterInterface;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadata;

interface SetterResolverInterface
{
    /**
     * @template D of object
     * @param  ClassMetadata<D>  $destinationMetaData
     *
     * @return SetterInterface|null
     */
    public function resolve(
        ClassMetadata $destinationMetaData,
        MapMetadata   $mapMetadata,
    ): ?SetterInterface;
}