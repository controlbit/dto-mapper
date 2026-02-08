<?php
declare(strict_types=1);

namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface DestinationFactoryInterface
{
    /**
     * @template S of object
     * @template D of object
     *
     * @param  S                     $source
     * @param  class-string<D>|null  $destination
     * @param  ClassMetadata<S>      $sourceClassMetadata
     *
     * @return D|class-string<D>|null
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $mapMetadataCollection,
        ?string               $destination,
    ): object|string|null;

}