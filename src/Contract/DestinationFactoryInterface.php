<?php
declare(strict_types=1);
namespace ControlBit\Dto\Contract;

use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

interface DestinationFactoryInterface
{
    /**
     * @template T of object
     * @param  class-string<T>|null  $destination
     *
     * @return T|class-string<T>|null
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $mapMetadataCollection,
        ?string               $destination,
    ): object|string|null;

}