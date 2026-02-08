<?php
declare(strict_types=1);

namespace ControlBit\Dto\Destination;

use ControlBit\Dto\Contract\DestinationFactoryInterface;
use ControlBit\Dto\Exception\RuntimeException;
use ControlBit\Dto\Mapper\Mapper;
use ControlBit\Dto\MetaData\Class\ClassMetadata;
use ControlBit\Dto\MetaData\Map\MapMetadataCollection;

final readonly class DestinationFactory
{
    /**
     * @param  iterable<DestinationFactoryInterface>  $delegates
     */
    public function __construct(private iterable $delegates)
    {
    }

    /**
     * @template D of object
     * @template S of object
     * @param  S                     $source
     * @param  class-string<D>|null  $destination
     * @param  ClassMetadata<S>      $sourceClassMetadata
     *
     * @return D
     */
    public function create(
        Mapper                $mapper,
        object                $source,
        ClassMetadata         $sourceClassMetadata,
        MapMetadataCollection $mapMetadataCollection,
        ?string               $destination,
    ): object {
        foreach ($this->delegates as $delegate) {
            $result = $delegate->create(
                $mapper,
                $source,
                $sourceClassMetadata,
                $mapMetadataCollection,
                $destination,
            );

            if (\is_object($result)) {
                return $result;
            }
        }

        throw new RuntimeException('Cannot generate destination object.');
    }
}